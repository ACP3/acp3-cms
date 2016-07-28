<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Cache;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Modules\ACP3\Users\Model\UserModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CacheResponseTrait
 * @package ACP3\Core\Cache
 */
trait CacheResponseTrait
{
    /**
     * @return UserModel
     */
    abstract protected function getUser();

    /**
     * @return Response
     */
    abstract protected function getResponse();

    /**
     * @return string
     */
    abstract protected function getApplicationMode();

    /**
     * @param int $lifetime
     */
    public function setCacheResponseCacheable($lifetime = 60)
    {
        $response = $this->getResponse();

        if ($this->getApplicationMode() === ApplicationMode::DEVELOPMENT) {
            $response->setPrivate();
            $lifetime = null;
        }

        $response
            ->setVary('X-User-Context-Hash')
            ->setPublic()
            ->setMaxAge($lifetime)
            ->setSharedMaxAge($lifetime);
    }
}
