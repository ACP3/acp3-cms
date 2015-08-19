<?php

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Accounts
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Admin
 */
class Accounts extends Core\Modules\AdminController
{
    /**
     * @var Newsletter\Model
     */
    protected $newsletterModel;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param Newsletter\Model                           $newsletterModel
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Newsletter\Model $newsletterModel)
    {
        parent::__construct($context);

        $this->newsletterModel = $newsletterModel;
    }

    /**
     * @param int $id
     */
    public function actionActivate($id)
    {
        $bool = $this->newsletterModel->update(['hash' => ''], $id, Newsletter\Model::TABLE_NAME_ACCOUNTS);

        $this->redirectMessages()->setMessage($bool, $this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'));
    }

    /**
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function($items) {
                $bool = false;
                foreach ($items as $item) {
                    $bool = $this->newsletterModel->delete($item, '', Newsletter\Model::TABLE_NAME_ACCOUNTS);
                }

                return $bool;
            }
        );
    }

    public function actionIndex()
    {
        $accounts = $this->newsletterModel->getAllAccounts();

        if (count($accounts) > 0) {
            $canDelete = $this->acl->hasPermission('admin/newsletter/accounts/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 3 : 2,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->user->getEntriesPerPage()
            ];
            $this->view->assign('datatable_config', $config);
            $this->view->assign('accounts', $accounts);
            $this->view->assign('can_delete', $canDelete);
        }
    }
}
