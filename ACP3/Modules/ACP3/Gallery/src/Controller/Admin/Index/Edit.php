<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

class Edit extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validation\GalleryFormValidation
     */
    protected $galleryFormValidation;
    /**
     * @var Gallery\Model\GalleryModel
     */
    protected $galleryModel;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext               $context
     * @param \ACP3\Core\Helpers\Forms                                    $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                $formTokenHelper
     * @param \ACP3\Modules\ACP3\Gallery\Validation\GalleryFormValidation $galleryFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Gallery\Model\GalleryModel $galleryModel,
        Gallery\Validation\GalleryFormValidation $galleryFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->galleryModel = $galleryModel;
        $this->galleryFormValidation = $galleryFormValidation;
        $this->formsHelper = $formsHelper;
    }

    /**
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        $gallery = $this->galleryModel->getOneById($id);

        if (!empty($gallery)) {
            $this->title->setPageTitlePrefix($gallery['title']);

            return [
                'active' => $this->formsHelper->yesNoCheckboxGenerator('active', $gallery['active']),
                'form' => \array_merge($gallery, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
                'SEO_URI_PATTERN' => Gallery\Helpers::URL_KEY_PATTERN_GALLERY,
                'SEO_ROUTE_NAME' => \sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $id),
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->galleryFormValidation
                ->setUriAlias(\sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $id))
                ->validate($formData);

            $formData['user_id'] = $this->user->getUserId();

            return $this->galleryModel->save($formData, $id);
        });
    }
}