<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

class Edit extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Helpers
     */
    protected $permissionsHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\AuthenticationModel
     */
    protected $authenticationModel;
    /**
     * @var Users\Model\UsersModel
     */
    protected $usersModel;
    /**
     * @var Users\Helpers\Forms
     */
    private $userFormsHelpers;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext           $context
     * @param \ACP3\Core\Helpers\FormToken                            $formTokenHelper
     * @param \ACP3\Core\Helpers\Secure                               $secureHelper
     * @param \ACP3\Core\Helpers\Forms                                $formsHelpers
     * @param \ACP3\Modules\ACP3\Users\Model\AuthenticationModel      $authenticationModel
     * @param \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Permissions\Helpers                  $permissionsHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\Forms $formsHelpers,
        Users\Helpers\Forms $userFormsHelpers,
        Users\Model\AuthenticationModel $authenticationModel,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AdminFormValidation $adminFormValidation,
        Permissions\Helpers $permissionsHelpers
    ) {
        parent::__construct($context, $formsHelpers);

        $this->formTokenHelper = $formTokenHelper;
        $this->secureHelper = $secureHelper;
        $this->authenticationModel = $authenticationModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
        $this->usersModel = $usersModel;
        $this->userFormsHelpers = $userFormsHelpers;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $user = $this->user->getUserInfo($id);

        if (!empty($user)) {
            $this->title->setPageTitlePrefix($user['nickname']);

            $userRoles = $this->acl->getUserRoleIds($id);
            $this->view->assign(
                $this->userFormsHelpers->fetchUserSettingsFormFields(
                    $user['address_display'],
                    $user['birthday_display'],
                    $user['country_display'],
                    $user['mail_display']
                )
            );
            $this->view->assign(
                $this->userFormsHelpers->fetchUserProfileFormFields(
                    $user['birthday'],
                    $user['country'],
                    $user['gender']
                )
            );

            return [
                'roles' => $this->fetchUserRoles($userRoles),
                'super_user' => $this->fetchIsSuperUser($user['super_user']),
                'contact' => $this->userFormsHelpers->fetchContactDetails(
                    $user['mail'],
                    $user['website'],
                    $user['icq'],
                    $user['skype']
                ),
                'form' => \array_merge($user, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost($id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->adminFormValidation
                ->setUserId($id)
                ->validate($formData);

            $this->permissionsHelpers->updateUserRoles($formData['roles'], $id);

            if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat'])) {
                $salt = $this->secureHelper->salt(Users\Model\UserModel::SALT_LENGTH);
                $newPassword = $this->secureHelper->generateSaltedPassword($salt, $formData['new_pwd'], 'sha512');
                $formData['pwd'] = $newPassword;
                $formData['pwd_salt'] = $salt;
            }

            $bool = $this->usersModel->save($formData, $id);

            $this->updateCurrentlyLoggedInUserCookie($id);

            return $bool;
        });
    }

    /**
     * @param int $userId
     */
    protected function updateCurrentlyLoggedInUserCookie($userId)
    {
        if ($userId == $this->user->getUserId() && $this->request->getCookies()->has(Users\Model\AuthenticationModel::AUTH_NAME)) {
            $user = $this->usersModel->getOneById($userId);
            $cookie = $this->authenticationModel->setRememberMeCookie(
                $userId,
                $user['remember_me_token']
            );
            $this->response->headers->setCookie($cookie);
        }
    }
}