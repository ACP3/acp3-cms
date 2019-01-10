<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Contact\Model\Repository\DataGridRepository
     */
    private $dataGridRepository;
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    /**
     * Index constructor.
     *
     * @param Core\Controller\Context\FrontendContext     $context
     * @param \ACP3\Core\Helpers\ResultsPerPage           $resultsPerPage
     * @param Contact\Model\Repository\DataGridRepository $dataGridRepository
     * @param \ACP3\Core\DataGrid\DataGrid                $dataGrid
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\ResultsPerPage $resultsPerPage,
        Contact\Model\Repository\DataGridRepository $dataGridRepository,
        Core\DataGrid\DataGrid $dataGrid
    ) {
        parent::__construct($context);

        $this->dataGridRepository = $dataGridRepository;
        $this->dataGrid = $dataGrid;
        $this->resultsPerPage = $resultsPerPage;
    }

    public function execute()
    {
        $input = (new Core\DataGrid\Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#contact-data-grid')
            ->setResourcePathDelete('admin/contact/index/delete');

        $this->addDataGridColumns($input);

        return $this->dataGrid->render($input);
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     */
    protected function addDataGridColumns(Core\DataGrid\Input $input)
    {
        $input
            ->addColumn([
                'label' => $this->translator->t('system', 'date'),
                'type' => Core\DataGrid\ColumnRenderer\DateColumnRenderer::class,
                'fields' => ['date'],
                'default_sort' => true,
                'default_sort_direction' => 'desc',
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'name'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['name'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'email_address'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['mail'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'message'),
                'type' => Core\DataGrid\ColumnRenderer\Nl2pColumnRenderer::class,
                'fields' => ['message'],
                'class' => 'datagrid-column__max-width',
            ], 10)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 5)
        ;
    }
}
