<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Test\View\Block\Frontend;

use ACP3\Core\Helpers\PageBreaks;
use ACP3\Core\Http\Request;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Test\View\Block\AbstractBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Articles\View\Block\Frontend\ArticleDetailsBlock;

class ArticleDetailsBlockTest extends AbstractBlockTest
{
    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;
    /**
     * @var PageBreaks|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pageBreaksHelper;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->pageBreaksHelper = $this->getMockBuilder(PageBreaks::class)
            ->disableOriginalConstructor()
            ->setMethods(['splitTextIntoPages'])
            ->getMock();

        $this->pageBreaksHelper->expects($this->once())
            ->method('splitTextIntoPages')
            ->willReturn([]);
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new ArticleDetailsBlock($this->context, $this->request, $this->pageBreaksHelper);
    }

    public function testRenderReturnsArray()
    {
        $this->block->setData(['title' => 'foo', 'text' => 'bar']);

        parent::testRenderReturnsArray();
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $this->block->setData(['title' => 'foo', 'text' => 'bar']);

        parent::testRenderReturnsArrayWithExpectedKeys();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'page',
        ];
    }
}
