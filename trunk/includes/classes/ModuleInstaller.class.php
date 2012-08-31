<?php
/**
 * Module Installer
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Module Installer Klasse
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
abstract class ACP3_ModuleInstaller {

	/**
	 * Die bei der Installation an das Modul zugewiesene ID
	 *
	 * @var integer
	 */
	protected $module_id = null;

	/**
	 * Ressourcen, welche vom standardmäßigen Namensschema abweichen
	 * oder spezielle Berechtigungen benötigen
	 *
	 * @var array
	 */
	protected $special_resources = array();

	function __construct() {
		global $db;

		$module = $db->select('id', 'modules', 'name = \'' . $db->escape($this->getName()) . '\'');
		$this->setModuleId(!empty($module[0]['id']) ? $module[0]['id'] : 0);
	}

	/**
	 * Setzt die ID eines Moduls
	 *
	 * @param mixed $module_id
	 */
	public function setModuleId($module_id)
	{
		$this->module_id = (int) $module_id;
	}

	/**
	 * Gibt die ID eines Moduls zurück
	 *
	 * @return integer
	 */
	public function getModuleId()
	{
		return (int) $this->module_id;
	}

	/**
	 * Methode zum Installieren des Moduls
	 *
	 * @return boolean
	 */
	public function install() {
		$bool1 = $this->executeSqlQueries($this->createTables());
		$bool2 = $this->addToModulesTable();
		$bool3 = $this->installSettings($this->settings());
		$bool4 = $this->addResources();

		return $bool1 && $bool2 && $bool3 && $bool4;
	}

	/**
	 * Methode zum Deinstallieren des Moduls
	 *
	 * @return boolean
	 */
	public function uninstall() {
		$bool1 = $this->executeSqlQueries($this->removeTables());
		$bool2 = $this->removeFromModulesTable();
		$bool3 = $this->removeSettings();
		$bool4 = $this->removeResources();

		return $bool1 && $bool2 && $bool3 && $bool4;
	}

	/**
	 * Führt die in $queries als Array übergebenen SQL-Statements aus
	 *
	 * @param array $queries
	 * @return boolean
	 */
	protected function executeSqlQueries(array $queries) {
		global $db;

		if (count($queries) > 0) {
			$search = array('{engine}', '{charset}');
			$replace = array('ENGINE=MyISAM', 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`');

			$db->link->beginTransaction();
			foreach ($queries as $query) {
				$bool = $db->query(str_replace($search, $replace, $query), 0);
				if ($bool === false) {
					$db->link->rollBack();
					return false;
				}
			}
			$db->link->commit();
		}
		return true;
	}

	/**
	 * Fügt die zu einen Modul zugehörigen Ressourcen ein
	 *
	 * @return boolean
	 */
	protected function addResources() {
		global $db;

		$mod_name = $db->select('name', 'modules', 'id = ' . $this->module_id);

		if (!empty($mod_name)) {
			$modules = scandir(MODULES_DIR . $mod_name[0]['name']);
			foreach ($modules as $row) {
				if ($row !== '.' && $row !== '..' && $row !== 'install.class.php') {
					// Erweiterungen
					$path = MODULES_DIR . $mod_name[0]['name'] . '/';
					if (is_dir($path . $row) === true && $row === 'extensions') {
						if (is_file($path . 'extensions/search.php') === true)
							$db->insert('acl_resources', array('id' => '', 'module_id' => $this->module_id, 'page' => 'extensions/search', 'params' => '', 'privilege_id' => 1));
						if (is_file($path . 'extensions/feeds.php') === true)
							$db->insert('acl_resources', array('id' => '', 'module_id' => $this->module_id, 'page' => 'extensions/feeds', 'params' => '', 'privilege_id' => 1));
					// Normale Moduldateien
					} elseif (strpos($row, '.php') !== false) {
						// .php entfernen
						$row = substr($row, 0, -4);
						if (isset($this->special_resources[$row])) {
							$privilege_id = $this->special_resources[$row];
						} else {
							$privilege_id = 1;

							if (strpos($row, 'create') === 0)
								$privilege_id = 2;
							if (strpos($row, 'acp_') === 0)
								$privilege_id = 3;
							if (strpos($row, 'acp_create') === 0 || strpos($row, 'acp_order') === 0)
								$privilege_id = 4;
							elseif (strpos($row, 'acp_edit') === 0)
								$privilege_id = 5;
							elseif (strpos($row, 'acp_delete') === 0)
								$privilege_id = 6;
							elseif (strpos($row, 'acp_settings') === 0)
								$privilege_id = 7;
						}
						$db->insert('acl_resources', array('id' => '', 'module_id' => $this->module_id, 'page' => $row, 'params' => '', 'privilege_id' => $privilege_id));
					}
				}
			}

			// Regeln für die Rollen setzen
			$roles = $db->select('id', 'acl_roles');
			$privileges = $db->select('id', 'acl_privileges');
			foreach ($roles as $role) {
				foreach ($privileges as $privilege) {
					$permission = 0;
					if ($role['id'] == 1 && ($privilege['id'] == 1 || $privilege['id'] == 2))
						$permission = 1;
					if ($role['id'] > 1 && $role['id'] < 4)
						$permission = 2;
					if ($role['id'] == 3 && $privilege['id'] == 3)
						$permission = 1;
					if ($role['id'] == 4)
						$permission = 1;

					$db->insert('acl_rules', array('id' => '', 'role_id' => $role['id'], 'module_id' => $this->module_id, 'privilege_id' => $privilege['id'], 'permission' => $permission));
				}
			}

			ACP3_Cache::purge(0, 'acl');

			return true;
		}

		return false;
	}

	/**
	 * Löscht die zu einem Modul zugehörigen Ressourcen
	 *
	 * @return boolean
	 */
	protected function removeResources() {
		global $db;

		$bool = $db->delete('acl_resources', 'module_id = ' . $this->module_id);
		$bool2 = $db->delete('acl_rules', 'module_id = ' . $this->module_id);

		ACP3_Cache::purge(0, 'acl');

		return $bool && $bool2;
	}

	/**
	 * Installiert die zu einem Module zugehörigen Einstellungen
	 * 
	 * @param array $settings
	 * @return boolean
	 */
	protected function installSettings(array $settings) {
		global $db;

		if (count($settings) > 0) {
			$db->link->beginTransaction();
			$bool = false;
			foreach ($settings as $key => $value) {
				$bool = $db->insert('settings', array('id' => '', 'module_id' => $this->module_id, 'name' => $key, 'value' => $value));
				if ($bool === false) {
					$db->link->rollBack();
					return false;
				}
			}
			$db->link->commit();
		}
		return true;
	}

	/**
	 * Löscht die zu einem Module zugehörigen Einstellungen
	 * 
	 * @return boolean
	 */
	protected function removeSettings() {
		global $db;

		return (bool) $db->delete('settings', 'module_id = ' . ((int) $this->module_id));
	}

	/**
	 * Fügt ein Modul zur modules DB-Tabelle hinzu
	 *
	 * @return boolean
	 */
	protected function addToModulesTable() {
		global $db;

		// Modul in die Modules-SQL-Tabelle eintragen
		$bool = $db->insert('modules', array('id' => '', 'name' => $this->getName(), 'version' => $this->getSchemaVersion(), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	/**
	 * Löscht ein Modul aus der modules DB-Tabelle
	 * @return boolean
	 */
	protected function removeFromModulesTable() {
		global $db;

		return (bool) $db->delete('modules', 'id = ' . ((int) $this->module_id));
	}

	/**
	 * Führt die in der Methode schemaUpdates() enthaltenen Tabellenänderungen aus
	 *
	 * @param array $queries
	 * @return integer
	 */
	public function updateSchema() {
		global $db;

		$module = $db->select('version', 'modules', 'name = \'' . $db->escape($this->getName()) . '\'');
		$installed_schema_version = isset($module[0]['version']) ? (int) $module[0]['version'] : 0;
		$result = -1;

		// Falls eine Methode zum Umbenennen des Moduls existiert,
		// diese mit der aktuell installierten Schemaverion aufrufen
		$module_names = $this->renameModule();
		if (count($module_names) > 0) {
			$result = $this->interateOverSchemaUpdates($module_names, $installed_schema_version);
		}

		$queries = $this->schemaUpdates();
		if (count($queries) > 0) {
			// Nur für den Fall der Fälle... ;)
			ksort($queries);

			$result = $this->interateOverSchemaUpdates($queries, $installed_schema_version);
		}
		return $result;
	}

	/**
	 * 
	 * @param array $schema_updates
	 * @param integer $installed_schema_version
	 * @return integer
	 */
	private function interateOverSchemaUpdates(array $schema_updates, $installed_schema_version) {
		$result = -1;
		foreach ($schema_updates as $new_schema_version => $queries) {
			// Schema-Änderungen nur für neuere Versionen durchführen
			if ($installed_schema_version < $new_schema_version) {
				// Einzelne Schema-Änderung bei einer Version
				if (!empty($queries) && is_array($queries) === false) {
					$result = $this->executeSqlQueries((array) $queries) === true ? 1 : 0;
					if ($result !== 0)
						$this->setNewSchemaVersion($new_schema_version);
				// Mehrere Schema-Änderungen bei einer Version
				} else {
					if (!empty($queries) && is_array($queries) === true) 
						$result = $this->executeSqlQueries($queries) === true ? 1 : 0;
						// Falls kein Fehler aufgetreten ist, die Schema Version des Moduls erhöhen
					if ($result !== 0)
						$this->setNewSchemaVersion($new_schema_version);
				}
			}
		}
		return $result;
	}

	/**
	 * Setzt die DB-Schema-Version auf die neue Versionsnummer
	 *
	 * @param integer $new_version
	 * @return boolean
	 */
	public function setNewSchemaVersion($new_version) {
		global $db;

		return (bool) $db->update('modules', array('version' => (int) $new_version), 'name = \'' . $db->escape($this->getName()) . '\'');
	}

	/**
	 * Methodenstub zum Umbenennen eines Moduls
	 *
	 * @return array
	 */
	public function renameModule() {
		return array();
	}

	/**
	 * Liefert den Modulnamen zurück
	 */
	abstract protected function getName();

	/**
	 * Liefert die DB-Schema-Version des Moduls zurück
	 */
	abstract protected function getSchemaVersion();

	/**
	 * Liefert ein Array mit den zu erstellenden Datenbanktabellen des Moduls zurück
	 */
	abstract protected function createTables();

	/**
	 * Liefert ein Array mit den zu löschenden Datenbanktabellen des Moduls zurück
	 */
	abstract protected function removeTables();

	/**
	 * Liefert ein Array mit den zu erstellenden Moduleinstellungen zurück
	 */
	abstract protected function settings();

	/**
	 * Aktualisiert die Tabellen und Einstellungen eines Moduls auf eine neue Version
	 */
	abstract protected function schemaUpdates();
}