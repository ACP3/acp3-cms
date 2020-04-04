<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Pics extends AbstractAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository
     */
    protected $galleryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    protected $galleryCache;

    /**
     * Pics constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                 $context
     * @param \ACP3\Core\Date                                               $date
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository $galleryRepository
     * @param \ACP3\Modules\ACP3\Gallery\Cache                              $galleryCache
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Gallery\Model\Repository\GalleryRepository $galleryRepository,
        Gallery\Cache $galleryCache
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->galleryRepository = $galleryRepository;
        $this->galleryCache = $galleryCache;
    }

    /**
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        if ($this->galleryRepository->galleryExists($id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            $gallery = $this->galleryRepository->getOneById($id);

            $this->breadcrumb
                ->append($this->translator->t('gallery', 'gallery'), 'gallery')
                ->append(
                    $gallery['title'],
                    $this->request->getQuery()
                );
            $this->title->setPageTitle($gallery['title']);

            return [
                'pictures' => $this->galleryCache->getCache($id),
                'gallery' => $gallery,
                'overlay' => (int) $this->settings['overlay'],
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}