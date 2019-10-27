<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\Assets;

use ACP3\Core\Assets\Cache;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\Theme;
use PHPUnit\Framework\TestCase;

class FileResolverTest extends TestCase
{
    /**
     * @var FileResolver
     */
    private $fileResolver;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $assetsCache;
    /**
     * @var ApplicationPath
     */
    private $appPath;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $themeMock;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->appPath = new ApplicationPath(ApplicationMode::DEVELOPMENT);
        $this->appPath
            ->setDesignRootPathInternal(ACP3_ROOT_DIR . 'tests/designs/');

        $this->fileResolver = new FileResolver(
            $this->assetsCache,
            $this->appPath,
            $this->themeMock
        );
    }

    private function setUpMockObjects(): void
    {
        $this->assetsCache = $this->createMock(Cache::class);
        $this->themeMock = $this->createMock(Theme::class);
    }

    public function testResolveTemplatePath(): void
    {
        $this->setUpThemeMockExpectations('acp3', ['acp3']);

        $expected = $this->appPath->getModulesDir() . 'ACP3/System/Resources/View/Partials/breadcrumb.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/breadcrumb.tpl');
        $this->assertSamePath($expected, $actual);
    }

    private function assertSamePath(string $expected, string $actual): void
    {
        $this->assertEquals(
            \str_replace('\\', '/', $expected),
            \str_replace('\\', '/', $actual)
        );
    }

    public function testResolveTemplatePathWithInheritance(): void
    {
        $this->setUpThemeMockExpectations('acp3', ['acp3']);

        $expected = $this->appPath->getDesignRootPathInternal() . 'acp3/System/View/Partials/mark.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/mark.tpl');
        $this->assertSamePath($expected, $actual);
    }

    public function testResolveTemplatePathWithMultipleInheritance(): void
    {
        $this->themeMock->expects($this->any())
            ->method('getCurrentTheme')
            ->willReturn('acp3-inherit');
        $this->themeMock->expects($this->any())
            ->method('getThemeDependencies')
            ->willReturnOnConsecutiveCalls(['acp3-inherit', 'acp3'], ['acp3']);

        $expected = ACP3_ROOT_DIR . 'tests/designs/acp3/layout.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('layout.tpl');
        $this->assertSamePath($expected, $actual);
    }

    private function setUpThemeMockExpectations(string $themeName, array $dependencies): void
    {
        $this->themeMock->expects($this->any())
            ->method('getCurrentTheme')
            ->willReturn($themeName);
        $this->themeMock->expects($this->any())
            ->method('getThemeDependencies')
            ->with($themeName)
            ->willReturn($dependencies);
    }

    public function testResolveTemplatePathWithDeeplyNestedFolderStructure(): void
    {
        $this->themeMock->expects($this->any())
            ->method('getCurrentTheme')
            ->willReturn('acp3-inherit');
        $this->themeMock->expects($this->any())
            ->method('getThemeDependencies')
            ->willReturnOnConsecutiveCalls(['acp3-inherit', 'acp3'], ['acp3']);

        $expected = ACP3_ROOT_DIR . 'tests/designs/acp3-inherit/System/View/Partials/Foo/bar/baz.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/Foo/bar/baz.tpl');
        $this->assertSamePath($expected, $actual);
    }
}
