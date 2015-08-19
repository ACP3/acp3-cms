<?php

namespace ACP3\Modules\ACP3\Newsletter\Controller;

use ACP3\Core;
use ACP3\Core\Modules\FrontendController;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Archive
 * @package ACP3\Modules\ACP3\Newsletter\Controller
 */
class Archive extends Core\Modules\FrontendController
{
    /**
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var Newsletter\Model
     */
    protected $newsletterModel;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param Core\Pagination                               $pagination
     * @param Newsletter\Model                              $newsletterModel
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Pagination $pagination,
        Newsletter\Model $newsletterModel)
    {
        parent::__construct($context);

        $this->pagination = $pagination;
        $this->newsletterModel = $newsletterModel;
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDetails($id)
    {
        $newsletter = $this->newsletterModel->getOneById($id, 1);

        if (!empty($newsletter)) {
            $this->breadcrumb
                ->append($this->lang->t('newsletter', 'index'), 'newsletter')
                ->append($this->lang->t('newsletter', 'frontend_archive_index'), 'newsletter/archive')
                ->append($newsletter['title']);

            $this->view->assign('newsletter', $newsletter);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $this->pagination->setTotalResults($this->newsletterModel->countAll(1));
        $this->pagination->display();

        $this->view->assign('newsletters', $this->newsletterModel->getAll(1, POS, $this->user->getEntriesPerPage()));
    }
}
