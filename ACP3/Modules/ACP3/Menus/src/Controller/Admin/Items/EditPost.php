<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\Context;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Menus\Services\MenuItemUpsertService;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class EditPost extends AbstractWidgetAction
{
    public function __construct(
        Context $context,
        private readonly FormAction $actionHelper,
        private readonly MenuItemUpsertService $menuItemUpsertService,
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|string|Response
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(int $id): array|string|Response
    {
        return $this->actionHelper->handleSaveAction(
            fn () => $this->menuItemUpsertService->upsert($this->request->getPost()->all(), $id),
            'acp/menus'
        );
    }
}
