<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Menus\Model\MenusModel
     */
    protected $menusModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Menus\Model\MenusModel $menusModel
    ) {
        parent::__construct($context);

        $this->menusModel = $menusModel;
    }

    /**
     * @param string $action
     *
     * @return mixed
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->menusModel->delete($items);
            }
        );
    }
}