<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Helper;

use ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository;
use ACP3\Modules\ACP3\Seo\Model\SeoModel;

class UriAliasManager
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository
     */
    protected $seoRepository;
    /**
     * @var SeoModel
     */
    protected $seoModel;

    /**
     * UriAliasManager constructor.
     *
     * @param SeoModel $seoModel
     * @param \ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository $seoRepository
     */
    public function __construct(
        SeoModel $seoModel,
        SeoRepository $seoRepository
    ) {
        $this->seoRepository = $seoRepository;
        $this->seoModel = $seoModel;
    }

    /**
     * Deletes the given URL alias
     *
     * @param string $path
     *
     * @return boolean
     */
    public function deleteUriAlias($path)
    {
        $path .= $this->preparePath($path);
        $seo = $this->seoRepository->getOneByUri($path);

        return !empty($seo) && $this->seoModel->delete($seo['id']) !== false;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function preparePath($path)
    {
        return !\preg_match('/\/$/', $path) ? '/' : '';
    }

    /**
     * Inserts/Updates a given URL alias
     *
     * @param string $path
     * @param string $alias
     * @param string $keywords
     * @param string $description
     * @param int $robots
     * @param string $title
     * @return bool
     */
    public function insertUriAlias($path, $alias, $keywords = '', $description = '', $robots = 0, $title = '')
    {
        $path .= $this->preparePath($path);
        $data = [
            'alias' => $alias,
            'seo_title' => $title,
            'seo_keywords' => $keywords,
            'seo_description' => $description,
            'seo_robots' => (int)$robots,
        ];

        $seo = $this->seoRepository->getOneByUri($path);

        if (!empty($seo)) {
            $data['uri'] = $seo['uri'];
            $bool = $this->seoModel->save($data, $seo['id']);
        } else {
            $data['uri'] = $path;
            $bool = $this->seoModel->save($data);
        }

        return $bool !== false;
    }
}
