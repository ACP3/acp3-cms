<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\ViewProviders;

use ACP3\Modules\ACP3\Articles\Cache;

class ArticleFullViewProvider
{
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache
     */
    private $articlesCache;

    public function __construct(Cache $articlesCache)
    {
        $this->articlesCache = $articlesCache;
    }

    public function __invoke(int $articleId): array
    {
        return [
            'sidebar_article' => $this->articlesCache->getCache($articleId),
        ];
    }
}