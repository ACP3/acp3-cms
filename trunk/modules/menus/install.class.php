<?php

class ACP3_MenusModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'menus';
	private $schema_version = 31;

	public function renameModule() {
		return array(
			31 => "UPDATE `{pre}modules` SET name = 'menus' WHERE name = 'menu_items';"
		);
	}

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}menu_items` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`mode` TINYINT(1) UNSIGNED NOT NULL,
				`block_id` INT(10) UNSIGNED NOT NULL,
				`root_id` INT(10) UNSIGNED NOT NULL,
				`parent_id` INT(10) UNSIGNED NOT NULL,
				`left_id` INT(10) UNSIGNED NOT NULL,
				`right_id` INT(10) UNSIGNED NOT NULL,
				`display` TINYINT(1) UNSIGNED NOT NULL,
				`title` VARCHAR(120) NOT NULL,
				`uri` VARCHAR(120) NOT NULL,
				`target` TINYINT(1) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), INDEX `foreign_block_id` (`block_id`)
			) {engine} {charset};",
			"CREATE TABLE `{pre}menus` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`index_name` VARCHAR(10) NOT NULL,
				`title` VARCHAR(120) NOT NULL,
				PRIMARY KEY (`id`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array(
			"DROP TABLE `{pre}menus`;",
			"DROP TABLE `{pre}menu_items`;"
		);
	}

	protected function settings() {
		return array();
	}

	protected function removeSettings() {
		return true;
	}

	protected function schemaUpdates() {
		return array(
			31 => array(
				"UPDATE `{pre}acl_resources` SET page = 'acp_create_item' WHERE module_id = " . $this->getModuleId() . " AND page = 'acp_create_block';",
				"UPDATE `{pre}acl_resources` SET page = 'acp_delete_item' WHERE module_id = " . $this->getModuleId() . " AND page = 'acp_delete_blocks';",
				"UPDATE `{pre}acl_resources` SET page = 'acp_edit_item' WHERE module_id = " . $this->getModuleId() . " AND page = 'acp_edit_block';",
				"DELETE  FROM `{pre}acl_resources` WHERE page = 'acp_list_blocks' AND module_id = " . $this->getModuleId() . ";",
				"RENAME TABLE `{pre}menu_items_blocks` TO `{pre}menus`"
			)
		);
	}
}