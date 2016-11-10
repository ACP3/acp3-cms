<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Test\Event\Listener;


use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Event\Listener\InvalidatePageCacheOnModelAfterSaveListener;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;

class InvalidatePageCacheOnModelAfterSaveListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InvalidatePageCacheOnModelAfterSaveListener
     */
    private $invalidatePageCache;
    /**
     * @var ApplicationPath
     */
    private $applicationPath;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $settingsMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $canUsePageCacheMock;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->invalidatePageCache = new InvalidatePageCacheOnModelAfterSaveListener(
            $this->applicationPath,
            $this->settingsMock,
            $this->canUsePageCacheMock
        );
    }

    private function setUpMockObjects()
    {
        $this->applicationPath = new ApplicationPath(ApplicationMode::DEVELOPMENT);
        $this->settingsMock = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();
        $this->canUsePageCacheMock = $this->getMockBuilder(CanUsePageCache::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testDisabledPageCache()
    {
        $this->setUpCanUsePageCacheMockExpectations(false);
        $this->setUpSettingsMockExpectations();

        $this->invalidatePageCache->invalidatePageCache();
    }

    private function setUpCanUsePageCacheMockExpectations($cacheEnabled = true)
    {
        $this->canUsePageCacheMock->expects($this->once())
            ->method('canUsePageCache')
            ->willReturn($cacheEnabled);
    }

    private function setUpSettingsMockExpectations($methodCalls = 0, $purgeMode = 1)
    {
        $this->settingsMock->expects($this->exactly($methodCalls))
            ->method('getSettings')
            ->with('system')
            ->willReturn(['page_cache_purge_mode' => $purgeMode]);
    }

    public function testManualPageCachePurge()
    {
        $this->setUpCanUsePageCacheMockExpectations(true);
        $this->setUpSettingsMockExpectations(1, 2);

        $this->invalidatePageCache->invalidatePageCache();
    }

    public function testAutomaticPageCachePurge()
    {
        $this->setUpCanUsePageCacheMockExpectations(true);
        $this->setUpSettingsMockExpectations(1, 2);

        $this->settingsMock->expects($this->once())
            ->method('saveSettings')
            ->with(['page_cache_is_valid' => false], 'system');

        $this->invalidatePageCache->invalidatePageCache();
    }
}