<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Repository\S3;

use Aws\S3\S3Client;
use GuzzleHttp\Psr7;
use Illuminate\Support\Str;
use Psr\Http\Message\StreamInterface;

/**
 * Class Storage
 * Based on https://github.com/frostealth/yii2-aws-s3.
 *
 * PHP SDK not included, so you have to install it manually:
 * https://github.com/aws/aws-sdk-php
 * Remember to install all dependencies listed in
 * https://github.com/aws/aws-sdk-php/blob/master/composer.json
 */
class StorageHelper
{
    public const ACL_PRIVATE = 'private';
    public const ACL_PUBLIC_READ = 'public-read';
    public const ACL_PUBLIC_READ_WRITE = 'public-read-write';
    public const ACL_AUTHENTICATED_READ = 'authenticated-read';
    public const ACL_BUCKET_OWNER_READ = 'bucket-owner-read';
    public const ACL_BUCKET_OWNER_FULL_CONTROL = 'bucket-owner-full-control';

    public const ACL_POLICY_DEFAULT = 'default';
    public const ACL_POLICY_INHERIT = 'inherit';

    /**
     * @var \Aws\Credentials\CredentialsInterface|array|callable
     */
    public $credentials;

    /**
     * @var string
     */
    public $region;

    /**
     * @var string
     */
    public $bucket;

    /**
     * @var string
     */
    public $cdnHostname;

    /**
     * @var string
     */
    public $defaultAcl;

    /**
     * @var string
     */
    public $endpoint;

    /**
     * The Server-side encryption algorithm used when storing objects in S3.
     * Valid values: null|AES256|aws:kms
     * http://docs.aws.amazon.com/AmazonS3/latest/dev/serv-side-encryption.html
     * http://docs.aws.amazon.com/AmazonS3/latest/dev/UsingServerSideEncryption.html.
     *
     * @var string|null
     */
    public $encryption;

    /**
     * @var bool|array
     */
    public $debug = false;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var S3Client
     */
    private $client;

    /**
     * @throws \Exception
     */
    public function init()
    {
        if (empty($this->credentials)) {
            throw new \Exception('S3 credentials isn\'t set.');
        }

        if (empty($this->region)) {
            throw new \Exception('Region isn\'t set.');
        }

        if (empty($this->bucket)) {
            throw new \Exception('You must set bucket name.');
        }

        if (!empty($this->cdnHostname)) {
            $this->cdnHostname = rtrim($this->cdnHostname, '/');
        }

        $args = $this->prepareArgs($this->options, [
            'version' => '2006-03-01',
            'region' => $this->region,
            'endpoint' => $this->endpoint,
            'credentials' => $this->credentials,
            'debug' => $this->debug,
        ]);

        $this->client = new S3Client($args);

        // to use PHP functions like copy(), rename() etc.
        // https://docs.aws.amazon.com/aws-sdk-php/v3/guide/service/s3-stream-wrapper.html
        $this->client->registerStreamWrapper();

        if (!empty($this->encryption)) {
            // set default params for S3 StreamWrapper
            stream_context_set_default([
                's3' => [
                    'ServerSideEncryption' => $this->encryption,
                ],
            ]);
        }
    }

    /**
     * @return S3Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function put($key, $data = '', array $options = [])
    {
        $options = $this->applyAclPolicy($options);
        $args = $this->prepareArgs($options, [
            'Bucket' => $this->bucket,
            'Key' => $this->trimKey($key),
            'Body' => $data,
            'ServerSideEncryption' => $this->encryption,
        ]);

        return $this->execute('PutObject', $args);
    }

    public function get($key, $saveAs = null)
    {
        $args = $this->prepareArgs([
            'Bucket' => $this->bucket,
            'Key' => $this->trimKey($key),
            'SaveAs' => $saveAs,
            'ServerSideEncryption' => $this->encryption,
        ]);

        return $this->execute('GetObject', $args);
    }

    public function exist($key, array $options = [])
    {
        return $this->getClient()->doesObjectExist($this->bucket, $this->trimKey($key), $options);
    }

    public function getObjectAcl($key)
    {
        return $this->execute('getObjectAcl', [
            'Bucket' => $this->bucket,
            'Key' => $this->trimKey($key),
        ]);
    }

    public function getUrl($key)
    {
        return $this->getClient()->getObjectUrl($this->bucket, $this->trimKey($key));
    }

    public function getPresignedUrl($key, $expires)
    {
        $command = $this->getClient()->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $this->trimKey($key),
        ]);
        $request = $this->getClient()->createPresignedRequest($command, $expires);

        return (string) $request->getUri();
    }

    public function getCdnUrl($key)
    {
        return $this->cdnHostname . '/' . $this->trimKey($key);
    }

    public function getList($prefix = null, array $options = [])
    {
        $args = $this->prepareArgs($options, [
            'Bucket' => $this->bucket,
            'Prefix' => $prefix,
            'ServerSideEncryption' => $this->encryption,
        ]);

        return $this->execute('ListObjects', $args);
    }

    public function upload($key, $source, $acl = null, array $options = [])
    {
        return $this->getClient()->upload(
            $this->bucket,
            $this->trimKey($key),
            $this->toStream($source),
            !empty($acl) ? $acl : $this->defaultAcl,
            $options
        );
    }

    public function uploadAsync(
        $key,
        $source,
        $concurrency = null,
        $partSize = null,
        $acl = null,
        array $options = [],
    ) {
        $args = $this->prepareArgs($options, [
            'concurrency' => $concurrency,
            'part_size' => $partSize,
        ]);

        return $this->getClient()->uploadAsync(
            $this->bucket,
            $this->trimKey($key),
            $this->toStream($source),
            !empty($acl) ? $acl : $this->defaultAcl,
            $args
        );
    }

    /**
     * @param string $name
     *
     * @return \Aws\ResultInterface
     */
    protected function execute($name, array $args)
    {
        $command = $this->getClient()->getCommand($name, $args);

        return $this->getClient()->execute($command);
    }

    /**
     * @return array
     */
    protected function prepareArgs(array $a)
    {
        $result = [];
        $args = \func_get_args();

        foreach ($args as $item) {
            $item = array_filter($item);
            $result = array_replace($result, $item);
        }

        return $result;
    }

    /**
     * Create a new stream based on the input type.
     *
     * @param resource|string|StreamInterface $source path to a local file, resource or stream
     *
     * @return StreamInterface
     */
    protected function toStream($source)
    {
        if (\is_string($source)) {
            $source = Psr7\try_fopen($source, 'r+');
        }

        return Psr7\stream_for($source);
    }

    /**
     * @param string $key
     * @param bool   $handle - return boolean value on `true`, otherwise throw an exception
     *
     * @return \Aws\ResultInterface|bool
     */
    public function head($key, $handle = false)
    {
        $args = [
            'Bucket' => $this->bucket,
            'Key' => $this->trimKey($key),
        ];

        if (!$handle) {
            return $this->execute('HeadObject', $args);
        }
        /* @see \Aws\S3\S3Client::checkExistenceWithCommand(), moved here to avoid extra request */
        try {
            return $this->execute('HeadObject', $args);
        } catch (\Aws\S3\Exception\S3Exception $e) {
            if ($e->getStatusCode() >= 500) {
                throw $e;
            }

            return false;
        }
    }

    /**
     * @return \Aws\ResultInterface|bool
     */
    public function copy($key, $destination, $acl = null, array $options = [])
    {
        return $this->getClient()->copy(
            $this->bucket,
            $this->trimKey($key),
            $this->bucket,
            $destination,
            !empty($acl) ? $acl : $this->defaultAcl,
            $options
        );
    }

    public function delete(string $key)
    {
        return $this->execute('DeleteObject', [
            'Bucket' => $this->bucket,
            'Key' => $this->trimKey($key),
        ]);
    }

    public function batchDelete(string $key): void
    {
        $this->getClient()->deleteMatchingObjects($this->bucket, $this->trimKey($key));
    }

    /**
     * Trim leading slash from key.
     */
    public function trimKey(string $key): string
    {
        return ltrim($key, '/');
    }

    /**
     * Apply ACL policies based on given options.
     */
    protected function applyAclPolicy(array $options)
    {
        // specifying both ACL and Grant... options is not allowed
        foreach ($options as $name => $value) {
            if (Str::startsWith($name, 'Grant')) {
                return $options;
            }
        }

        if (!isset($options['ACL'])) {
            $options['ACL'] = $this->defaultAcl;
        }

        return $options;
    }
}
