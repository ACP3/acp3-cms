<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Extension;


use ACP3\Core\Date;
use ACP3\Core\Router\Router;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;
use ACP3\Modules\ACP3\Seo\Extension\AbstractSitemapAvailabilityExtension;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

class SitemapAvailabilityExtension extends AbstractSitemapAvailabilityExtension
{
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var GalleryRepository
     */
    protected $galleryRepository;
    /**
     * @var PictureRepository
     */
    protected $pictureRepository;

    /**
     * SitemapAvailabilityExtension constructor.
     * @param Date $date
     * @param Router $router
     * @param GalleryRepository $galleryRepository
     * @param PictureRepository $pictureRepository
     * @param MetaStatements $metaStatements
     */
    public function __construct(
        Date $date,
        Router $router,
        GalleryRepository $galleryRepository,
        PictureRepository $pictureRepository,
        MetaStatements $metaStatements
    ) {
        parent::__construct($router, $metaStatements);

        $this->date = $date;
        $this->galleryRepository = $galleryRepository;
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return Schema::MODULE_NAME;
    }

    protected function fetchSitemapUrls()
    {
        $this->addUrl('gallery/index/index');

        foreach ($this->galleryRepository->getAll($this->date->getCurrentDateTime()) as $result) {
            $this->addUrl('gallery/index/pics/id_' . $result['id'], $result['start']);

            foreach ($this->pictureRepository->getPicturesByGalleryId($result['id']) as $picture) {
                $this->addUrl('gallery/index/details/id_' . $picture['id']);
            }
        }
    }
}
