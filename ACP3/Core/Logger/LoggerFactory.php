<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Logger;


use ACP3\Core\Environment\ApplicationPath;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LoggerFactory
{
    /**
     * @var ApplicationPath
     */
    private $appPath;

    /**
     * LoggerFactory constructor.
     * @param ApplicationPath $appPath
     */
    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    /**
     * @param string $channel
     * @param string $level
     * @return LoggerInterface
     */
    public function create($channel, $level = LogLevel::DEBUG)
    {
        $fileName = $this->appPath->getCacheDir() . 'logs/' . $channel . '.log';

        $stream = new StreamHandler($fileName, $level);
        $stream->setFormatter(new LineFormatter(null, null, true));

        return new \Monolog\Logger($channel, [$stream]);
    }
}