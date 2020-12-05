<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Http\RequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AssetsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Assets
     */
    private $assets;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $eventDispatcherMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;

    protected function setup(): void
    {
        $this->setUpMockObjects();

        $theme = $this->createMock(ThemePathInterface::class);
        $theme->expects(self::once())
            ->method('getDesignPathInternal')
            ->willReturn(ACP3_ROOT_DIR . '/tests/designs/acp3/');
        $libraries = new Assets\Libraries($this->requestMock, $this->eventDispatcherMock);

        $this->assets = new Assets($theme, $libraries);
    }

    private function setUpMockObjects()
    {
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
    }

    public function testDefaultLibrariesEnabled()
    {
        $libraries = $this->assets->getEnabledLibrariesAsString();
        self::assertEquals('polyfill,jquery,bootstrap,ajax-form,font-awesome', $libraries);
    }

    public function testEnableDatepicker()
    {
        $this->assets->enableLibraries(['datetimepicker']);

        $libraries = $this->assets->getEnabledLibrariesAsString();
        self::assertEquals('polyfill,jquery,bootstrap,ajax-form,moment,datetimepicker,font-awesome', $libraries);
    }

    public function testFetchAdditionalThemeCssFiles()
    {
        $files = $this->assets->fetchAdditionalThemeCssFiles();

        self::assertEquals(['additional-style.css'], $files);
    }

    public function testFetchAdditionalThemeJsFiles()
    {
        $files = $this->assets->fetchAdditionalThemeJsFiles();

        self::assertEquals(['additional-script.js'], $files);
    }
}
