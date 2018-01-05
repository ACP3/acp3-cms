<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Settings;

use ACP3\Core\Cache\Cache;
use ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface;
use ACP3\Core\Model\Repository\SettingsAwareRepositoryInterface;
use ACP3\Core\Settings\Event\SettingsSaveEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Manages the various module settings
 */
class Settings implements SettingsInterface
{
    const CACHE_ID = 'settings';

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var ModuleAwareRepositoryInterface
     */
    protected $systemModuleRepository;
    /**
     * @var SettingsAwareRepositoryInterface
     */
    protected $systemSettingsRepository;
    /**
     * @var \ACP3\Core\Cache\Cache
     */
    protected $coreCache;
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Settings constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Cache $coreCache
     * @param ModuleAwareRepositoryInterface $systemModuleRepository
     * @param SettingsAwareRepositoryInterface $systemSettingsRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Cache $coreCache,
        ModuleAwareRepositoryInterface $systemModuleRepository,
        SettingsAwareRepositoryInterface $systemSettingsRepository
    ) {
        $this->coreCache = $coreCache;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->systemSettingsRepository = $systemSettingsRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function saveSettings(array $data, string $module): bool
    {
        $bool = $bool2 = false;
        $moduleId = $this->systemModuleRepository->getModuleId($module);
        if (!empty($moduleId)) {
            $this->eventDispatcher->dispatch('core.settings.save_before', new SettingsSaveEvent($module, $data));

            foreach ($data as $key => $value) {
                $updateValues = [
                    'value' => $value,
                ];
                $where = [
                    'module_id' => $moduleId,
                    'name' => $key,
                ];
                $bool = $this->systemSettingsRepository->update($updateValues, $where);
            }
            $bool2 = $this->saveCache();
        }

        return $bool !== false && $bool2 !== false;
    }

    /**
     * Saves the modules settings to the cache
     *
     * @return bool
     */
    private function saveCache(): bool
    {
        $settings = $this->systemSettingsRepository->getAllSettings();

        $data = [];
        foreach ($settings as $setting) {
            $data[$setting['module_name']][$setting['name']] = $setting['value'];
        }

        return $this->coreCache->save(static::CACHE_ID, $data);
    }

    /**
     * @inheritdoc
     */
    public function getSettings(string $module): array
    {
        if ($this->settings === []) {
            if ($this->coreCache->contains(static::CACHE_ID) === false) {
                $this->saveCache();
            }

            $this->settings = $this->coreCache->fetch(static::CACHE_ID);
        }

        return $this->settings[$module] ?? [];
    }
}
