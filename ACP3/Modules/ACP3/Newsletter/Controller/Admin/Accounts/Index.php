<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Accounts;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountDataGridRepository
     */
    protected $dataGridRepository;
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                            $context
     * @param \ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountDataGridRepository $dataGridRepository
     * @param \ACP3\Core\DataGrid\DataGrid                                             $dataGrid
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Newsletter\Model\Repository\AccountDataGridRepository $dataGridRepository,
        Core\DataGrid\DataGrid $dataGrid
    ) {
        parent::__construct($context);

        $this->dataGridRepository = $dataGridRepository;
        $this->dataGrid = $dataGrid;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $input = (new Core\DataGrid\Input())
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#newsletter-accounts-data-grid')
            ->setResourcePathDelete('admin/newsletter/accounts/delete');

        $this->addDataGridColumns($input);

        return [
            'grid' => $this->dataGrid->render($input),
            'show_mass_delete_button' => $input->getResultsCount() > 0,
        ];
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     */
    protected function addDataGridColumns(Core\DataGrid\Input $input)
    {
        $input
            ->addColumn([
                'label' => $this->translator->t('system', 'email_address'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['mail'],
                'default_sort' => true,
            ], 60)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'salutation'),
                'type' => Core\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer::class,
                'fields' => ['salutation'],
                'custom' => [
                    'search' => [0, 1, 2],
                    'replace' => [
                        '',
                        $this->translator->t('newsletter', 'salutation_female'),
                        $this->translator->t('newsletter', 'salutation_male'),
                    ],
                ],
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'first_name'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['first_name'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'last_name'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['last_name'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'status'),
                'type' => Newsletter\DataGrid\ColumnRenderer\AccountStatusColumnRenderer::class,
                'fields' => ['status'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 10);
    }
}
