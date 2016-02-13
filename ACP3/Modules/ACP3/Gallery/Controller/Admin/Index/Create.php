<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Index
 */
class Create extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository
     */
    protected $galleryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validation\GalleryFormValidation
     */
    protected $galleryFormValidation;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext                  $context
     * @param \ACP3\Core\Date                                             $date
     * @param \ACP3\Core\Helpers\FormToken                                $formTokenHelper
     * @param \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository          $galleryRepository
     * @param \ACP3\Modules\ACP3\Gallery\Validation\GalleryFormValidation $galleryFormValidation
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Gallery\Model\GalleryRepository $galleryRepository,
        Gallery\Validation\GalleryFormValidation $galleryFormValidation
    )
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->galleryRepository = $galleryRepository;
        $this->galleryFormValidation = $galleryFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->createPost($this->request->getPost()->all());
        }

        $defaults = [
            'title' => '',
            'start' => '',
            'end' => ''
        ];

        $this->formTokenHelper->generateFormToken();

        return [
            'SEO_FORM_FIELDS' => $this->seo->formFields(),
            'form' => array_merge($defaults, $this->request->getPost()->all())
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function createPost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $this->galleryFormValidation->validate($formData);

            $insertValues = [
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'user_id' => $this->user->getUserId(),
            ];

            $lastId = $this->galleryRepository->insert($insertValues);

            $this->seo->insertUriAlias(
                sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $lastId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->formTokenHelper->unsetFormToken();

            return $lastId;
        });
    }
}
