<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;

use ACP3\Core\View;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

class OnLayoutMetaListener
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;
    /**
     * @var View
     */
    protected $view;

    /**
     * OnCustomTemplateVariable constructor.
     */
    public function __construct(
        View $view,
        MetaStatements $metaStatements
    ) {
        $this->view = $view;
        $this->metaStatements = $metaStatements;
    }

    public function __invoke()
    {
        $this->view->assign('META', $this->metaStatements->getMetaTags());

        $this->view->displayTemplate('Seo/Partials/meta.tpl');
    }
}