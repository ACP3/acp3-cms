<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Errors\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractBlock;

class AccessForbiddenBlock extends AbstractBlock
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->breadcrumb->append($this->translator->t('errors', 'frontend_index_access_forbidden'));

        return [];
    }
}
