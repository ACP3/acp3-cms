<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;
use ACP3\Modules\ACP3\Seo\Helper\MetaFormFields;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin\Index
 */
class Edit extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository
     */
    protected $articleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache
     */
    protected $articlesCache;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var Menus\Helpers\MenuItemFormFields
     */
    protected $menuItemFormFieldsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields
     */
    protected $metaFormFieldsHelper;

    /**
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository $articleRepository
     * @param \ACP3\Modules\ACP3\Articles\Cache $articlesCache
     * @param \ACP3\Modules\ACP3\Articles\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Date $date,
        Core\Helpers\Forms $formsHelper,
        Articles\Model\Repository\ArticleRepository $articleRepository,
        Articles\Cache $articlesCache,
        Articles\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\FormToken $formTokenHelper
    ) {
        parent::__construct($context, $formsHelper);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
        $this->articlesCache = $articlesCache;
        $this->adminFormValidation = $adminFormValidation;
        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields $metaFormFieldsHelper
     */
    public function setMetaFormFieldsHelper(MetaFormFields $metaFormFieldsHelper)
    {
        $this->metaFormFieldsHelper = $metaFormFieldsHelper;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields $menuItemFormFieldsHelper
     *
     * @return $this
     */
    public function setMenuItemFormFieldsHelper(Menus\Helpers\MenuItemFormFields $menuItemFormFieldsHelper)
    {
        $this->menuItemFormFieldsHelper = $menuItemFormFieldsHelper;

        return $this;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository $menuItemRepository
     *
     * @return $this
     */
    public function setMenuItemRepository(Menus\Model\Repository\MenuItemRepository $menuItemRepository)
    {
        $this->menuItemRepository = $menuItemRepository;

        return $this;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $article = $this->articleRepository->getOneById($id);

        if (empty($article) === false) {
            $this->title->setPageTitlePostfix($article['title']);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $id);
            }

            return [
                'options' => $this->fetchOptions($id),
                'SEO_FORM_FIELDS' => $this->metaFormFieldsHelper
                    ? $this->metaFormFieldsHelper->formFields(sprintf(Articles\Helpers::URL_KEY_PATTERN, $id))
                    : [],
                'form' => array_merge($article, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param int $articleId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $articleId)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $articleId) {
            $this->adminFormValidation
                ->setUriAlias(sprintf(Articles\Helpers::URL_KEY_PATTERN, $articleId))
                ->validate($formData);

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => $this->get('core.helpers.secure')->strEncode($formData['title']),
                'text' => $this->get('core.helpers.secure')->strEncode($formData['text'], true),
                'user_id' => $this->user->getUserId(),
            ];

            $bool = $this->articleRepository->update($updateValues, $articleId);

            $this->articlesCache->saveCache($articleId);

            $this->insertUriAlias($formData, $articleId);
            $this->createOrUpdateMenuItem($formData, $articleId);

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $bool;
        });
    }

    /**
     * @param int $menuItemId
     * @return array
     */
    protected function fetchOptions($menuItemId)
    {
        $options = [];
        if ($this->acl->hasPermission('admin/menus/items/create') === true) {
            $menuItem = $this->menuItemRepository->getOneMenuItemByUri(
                sprintf(Articles\Helpers::URL_KEY_PATTERN, $menuItemId)
            );

            $options = $this->fetchCreateMenuItemOption(!empty($menuItem) ? 1 : 0);

            $this->view->assign(
                $this->menuItemFormFieldsHelper->createMenuItemFormFields(
                    $menuItem['block_id'],
                    $menuItem['parent_id'],
                    $menuItem['left_id'],
                    $menuItem['right_id'],
                    $menuItem['display']
                )
            );
        }

        return $options;
    }
}
