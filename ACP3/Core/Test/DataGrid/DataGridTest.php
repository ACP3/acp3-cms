<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\DataGrid;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\HeaderColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\ConfigProcessor;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\Formatter\MarkEntries;
use ACP3\Core\I18n\Translator;

class DataGridTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DataGrid
     */
    protected $dataGrid;
    /**
     * @var ConfigProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configProcessorMock;
    /**
     * @var ACL|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $aclMock;
    /**
     * @var Translator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $langMock;
    /**
     * @var Input
     */
    private $inputOptions;

    protected function setUp()
    {
        $this->configProcessorMock = $this->createMock(ConfigProcessor::class);
        $this->aclMock = $this->createMock(ACL::class);
        $this->langMock = $this->createMock(Translator::class);

        $this->dataGrid = new DataGrid(
            $this->configProcessorMock,
            $this->aclMock,
            $this->langMock
        );
        $this->inputOptions = (new Input())
            ->setIdentifier('#data-grid');

        parent::setUp();
    }

    /**
     * @return array
     */
    protected function getDefaultExpected()
    {
        return [
            'can_edit' => false,
            'can_delete' => false,
            'identifier' => 'data-grid',
            'header' => '',
            'config' => [],
            'results' => '',
            'num_results' => 0,
        ];
    }

    public function testRenderWithDefaults()
    {
        $this->aclMock
            ->expects($this->exactly(2))
            ->method('hasPermission')
            ->willReturn(false);

        $expected = $this->getDefaultExpected();

        $this->assertEquals($expected, $this->dataGrid->render($this->inputOptions));
    }

    public function testRenderWithOneTextColumn()
    {
        $this->aclMock
            ->expects($this->exactly(2))
            ->method('hasPermission')
            ->willReturn(false);

        /** @var MarkEntries|\PHPUnit_Framework_MockObject_MockObject $markEntriesMock */
        $markEntriesMock = $this->createMock(MarkEntries::class);

        $this->dataGrid->registerColumnRenderer(new HeaderColumnRenderer($markEntriesMock));
        $this->dataGrid->registerColumnRenderer(new TextColumnRenderer());

        $this->inputOptions->addColumn([
            'label' => 'Foo',
            'fields' => ['title'],
            'type' => TextColumnRenderer::class,
        ], 10);

        $expected = \array_merge(
            $this->getDefaultExpected(),
            [
                'header' => '<th>Foo</th>',
            ]
        );

        $this->assertEquals($expected, $this->dataGrid->render($this->inputOptions));
    }

    public function testRenderWithOneTextColumnAndData()
    {
        $data = [
            [
                'title' => 'Lorem Ipsum',
            ],
            [
                'title' => 'Lorem Ipsum Dolor',
            ],
        ];
        $this->aclMock
            ->expects($this->exactly(2))
            ->method('hasPermission')
            ->willReturn(false);

        /** @var MarkEntries|\PHPUnit_Framework_MockObject_MockObject $markEntriesMock */
        $markEntriesMock = $this->createMock(MarkEntries::class);

        $this->dataGrid->registerColumnRenderer(new HeaderColumnRenderer($markEntriesMock));
        $this->dataGrid->registerColumnRenderer(new TextColumnRenderer());

        $this->inputOptions->addColumn([
            'label' => 'Foo',
            'fields' => ['title'],
            'type' => TextColumnRenderer::class,
        ], 10);
        $this->inputOptions->setResults($data);

        $expected = \array_merge(
            $this->getDefaultExpected(),
            [
                'header' => '<th>Foo</th>',
                'results' => "<tr><td>Lorem Ipsum</td></tr>\n<tr><td>Lorem Ipsum Dolor</td></tr>\n",
                'num_results' => 2,
            ]
        );

        $this->assertEquals($expected, $this->dataGrid->render($this->inputOptions));
    }
}
