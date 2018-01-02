<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Test\View\Block\Admin;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Test\View\Block\AbstractDataGridBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Gallery\View\Block\Admin\GalleryPicturesDataGridBlock;

class GalleryPicturesDataGridBlockTest extends AbstractDataGridBlockTest
{
    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $acl = $this->getMockBuilder(ACLInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $galleryRepository = $this->getMockBuilder(GalleryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new GalleryPicturesDataGridBlock($this->context, $acl, $galleryRepository);
    }

    public function testRenderReturnsArray()
    {
        $this->block->setData(['results' => [], 'gallery_id' => 2]);

        parent::testRenderReturnsArray();
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $this->block->setData(['results' => [], 'gallery_id' => 2]);

        parent::testRenderReturnsArrayWithExpectedKeys();
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'gallery_id',
            'grid',
            'show_mass_delete_button'
        ];
    }
}