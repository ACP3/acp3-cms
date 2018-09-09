<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Gallery\Helpers;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var Gallery\Model\GalleryModel
     */
    protected $galleryModel;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                 $context
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository $pictureRepository
     * @param Gallery\Model\GalleryModel                                    $galleryModel
     * @param \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator          $thumbnailGenerator
     * @param \ACP3\Core\DataGrid\DataGrid                                  $dataGrid
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Model\GalleryModel $galleryModel,
        Gallery\Helper\ThumbnailGenerator $thumbnailGenerator,
        Core\DataGrid\DataGrid $dataGrid
    ) {
        parent::__construct($context);

        $this->pictureRepository = $pictureRepository;
        $this->galleryModel = $galleryModel;
        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->dataGrid = $dataGrid;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(int $id)
    {
        $gallery = $this->galleryModel->getOneById($id);

        if (!empty($gallery)) {
            $this->breadcrumb->append($gallery['title'], 'acp/gallery/pictures/index/id_' . $id);
            $this->title->setPageTitlePrefix($this->translator->t('gallery', 'admin_pictures_index'));

            $pictures = $this->pictureRepository->getPicturesByGalleryId($id);

            $input = (new Core\DataGrid\Input())
                ->setResults($pictures)
                ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
                ->setIdentifier('#gallery-pictures-data-grid')
                ->setResourcePathDelete('admin/gallery/pictures/delete/id_' . $id)
                ->setResourcePathEdit('admin/gallery/pictures/edit');

            $this->addDataGridColumns($input);

            return [
                'gallery_id' => $id,
                'grid' => $this->dataGrid->render($input),
                'show_mass_delete_button' => $input->getResultsCount() > 0,
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     */
    protected function addDataGridColumns(Core\DataGrid\Input $input)
    {
        $input
            ->addColumn([
                'label' => $this->translator->t('gallery', 'picture'),
                'type' => Core\DataGrid\ColumnRenderer\PictureColumnRenderer::class,
                'fields' => ['file'],
                'custom' => [
                    'callback' => function (string $fileName) {
                        return $this->thumbnailGenerator->generateThumbnail($fileName, 'thumb')->getFileWeb();
                    },
                ],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'description'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['description'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\DataGrid\ColumnRenderer\RouteColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'custom' => [
                    'path' => Helpers::URL_KEY_PATTERN_PICTURE,
                ],
            ], 10);

        if ($this->acl->hasPermission('admin/gallery/pictures/order')) {
            $input
                ->addColumn([
                    'label' => $this->translator->t('system', 'order'),
                    'type' => Core\DataGrid\ColumnRenderer\SortColumnRenderer::class,
                    'fields' => ['pic'],
                    'default_sort' => true,
                    'custom' => [
                        'route_sort_down' => 'acp/gallery/pictures/order/id_%d/action_down',
                        'route_sort_up' => 'acp/gallery/pictures/order/id_%d/action_up',
                    ],
                ], 20);
        }
    }
}
