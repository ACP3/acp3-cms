<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Permissions;

class CreatePost extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation
     */
    private $roleFormValidation;
    /**
     * @var Permissions\Model\RolesModel
     */
    private $roleModel;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;
    /**
     * @var Permissions\Model\AclPermissionModel
     */
    private $permissionModel;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Permissions\Model\RolesModel $rolesModel,
        Permissions\Model\AclPermissionModel $permissionModel,
        Permissions\Validation\RoleFormValidation $roleFormValidation
    ) {
        parent::__construct($context);

        $this->roleFormValidation = $roleFormValidation;
        $this->roleModel = $rolesModel;
        $this->actionHelper = $actionHelper;
        $this->permissionModel = $permissionModel;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();

            $this->roleFormValidation->validate($formData);

            $roleId = $this->roleModel->save($formData);
            $this->permissionModel->updatePermissions($formData['resources'], $roleId);

            return $roleId;
        });
    }
}
