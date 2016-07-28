<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core;

/**
 * Class AbstractFrontendAction
 * @package ACP3\Core\Controller
 */
abstract class AbstractFrontendAction extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Core\Assets
     */
    protected $assets;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    protected $breadcrumb;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    protected $title;
    /**
     * @var Core\Helpers\RedirectMessages
     */
    protected $redirectMessages;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    protected $actionHelper;
    /**
     * @var string
     */
    private $layout = 'layout.tpl';

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     */
    public function __construct(Context\FrontendContext $context)
    {
        parent::__construct($context);

        $this->assets = $context->getAssets();
        $this->breadcrumb = $context->getBreadcrumb();
        $this->title = $context->getTitle();
        $this->actionHelper = $context->getActionHelper();
    }

    /**
     * Helper function for initializing models, etc.
     *
     * @return $this
     * @throws \ACP3\Core\ACL\Exception\AccessForbiddenException
     */
    public function preDispatch()
    {
        $path = $this->request->getArea() . '/' . $this->request->getFullPathWithoutArea();

        if ($this->acl->hasPermission($path) === false) {
            throw new Core\ACL\Exception\AccessForbiddenException();
        }

        $this->view->assign([
            'PHP_SELF' => $this->appPath->getPhpSelf(),
            'REQUEST_URI' => $this->request->getServer()->get('REQUEST_URI'),
            'ROOT_DIR' => $this->appPath->getWebRoot(),
            'HOST_NAME' => $this->request->getHttpHost(),
            'ROOT_DIR_ABSOLUTE' => $this->request->getScheme() . '://' . $this->request->getHttpHost() . $this->appPath->getWebRoot(),
            'DESIGN_PATH' => $this->appPath->getDesignPathWeb(),
            'DESIGN_PATH_ABSOLUTE' => $this->appPath->getDesignPathAbsolute(),
            'UA_IS_MOBILE' => $this->request->getUserAgent()->isMobileBrowser(),
            'IN_ADM' => $this->request->getArea() === AreaEnum::AREA_ADMIN,
            'IS_HOMEPAGE' => $this->request->isHomepage(),
            'IS_AJAX' => $this->request->isXmlHttpRequest(),
            'LANG_DIRECTION' => $this->translator->getDirection(),
            'LANG' => $this->translator->getShortIsoCode(),
        ]);

        return parent::preDispatch();
    }

    protected function addCustomTemplateVarsBeforeOutput()
    {
        $this->view->assign('BREADCRUMB', $this->breadcrumb->getBreadcrumb());
        $this->view->assign('LAYOUT', $this->request->isXmlHttpRequest() ? 'system/ajax.tpl' : $this->getLayout());

        $this->eventDispatcher->dispatch(
            'core.controller.custom_template_variable',
            new Core\Controller\Event\CustomTemplateVariableEvent($this->view)
        );
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @return Core\Helpers\RedirectMessages
     */
    public function redirectMessages()
    {
        if (!$this->redirectMessages) {
            $this->redirectMessages = $this->get('core.helpers.redirect');
        }

        return $this->redirectMessages;
    }

    /**
     * @return \ACP3\Core\Http\RedirectResponse
     */
    public function redirect()
    {
        return $this->get('core.http.redirect_response');
    }
}
