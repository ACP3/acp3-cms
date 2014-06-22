<?php
namespace ACP3\Core;

use ACP3\Modules\System\Model;
use Doctrine\DBAL\Connection;

/**
 * Manages the various module settings
 *
 * @author Tino Goratsch
 */
class Config
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Model
     */
    protected $systemModel;
    /**
     * @var Cache2
     */
    protected $cache;
    /**
     * @var string
     */
    protected $module = '';

    public function __construct(Connection $db, $module)
    {
        $this->cache = new Cache2($module);
        $this->db = $db;
        $this->module = strtolower($module);
        $this->systemModel = new Model($db);
    }

    /**
     * Erstellt/Verändert die Konfigurationsdateien für die Module
     *
     * @param array $data
     * @return boolean
     */
    public function setSettings($data)
    {
        $bool = $bool2 = false;
        $moduleId = $this->systemModel->getModuleId($this->module);
        if (!empty($moduleId)) {
            foreach ($data as $key => $value) {
                $updateValues = array(
                    'value' => $value
                );
                $where = array(
                    'module_id' => $moduleId,
                    'name' => $key
                );
                $bool = $this->systemModel->update($updateValues, $where, Model::TABLE_NAME_SETTINGS);
            }
            $bool2 = $this->setCache();
        }

        return $bool !== false && $bool2 !== false;
    }

    /**
     * Gibt den Inhalt der Konfigurationsdateien der Module aus
     *
     * @return array
     */
    public function getSettings()
    {
        if ($this->cache->contains('settings') === false) {
            $this->setCache();
        }

        return $this->cache->fetch('settings');
    }

    /**
     * Outputs module settings as constants
     */
    public function getSettingsAsConstants()
    {
        $settings = $this->getSettings();
        foreach ($settings as $key => $value) {
            define('CONFIG_' . strtoupper($key), $value);
        }
    }

    /**
     * Setzt den Cache für die Einstellungen eines Moduls
     *
     * @return boolean
     */
    protected function setCache()
    {
        $settings = $this->systemModel->getSettingsByModuleName($this->module);
        $c_settings = count($settings);

        $data = array();
        for ($i = 0; $i < $c_settings; ++$i) {
            if (is_int($settings[$i]['value'])) {
                $data[$settings[$i]['name']] = (int)$settings[$i]['value'];
            } elseif (is_float($settings[$i]['value'])) {
                $data[$settings[$i]['name']] = (float)$settings[$i]['value'];
            } else {
                $data[$settings[$i]['name']] = $settings[$i]['value'];
            }
        }

        return $this->cache->save('settings', $data);
    }

}