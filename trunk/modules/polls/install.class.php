<?php

class ACP3_PollsModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'polls';
	private $schema_version = 31;

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}polls` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`start` DATETIME NOT NULL,
				`end` DATETIME NOT NULL,
				`title` VARCHAR(120) NOT NULL,
				`multiple` TINYINT(1) UNSIGNED NOT NULL,
				`user_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`)
			) {engine} {charset};",
			"CREATE TABLE `{pre}poll_answers` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`text` VARCHAR(120) NOT NULL,
				`poll_id` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), INDEX `foreign_poll_id` (`poll_id`)
			) {engine} {charset};",
			"CREATE TABLE `{pre}poll_votes` (
				`poll_id` INT(10) UNSIGNED NOT NULL,
				`answer_id` INT(10) UNSIGNED NOT NULL,
				`user_id` INT(10) UNSIGNED NOT NULL,
				`ip` VARCHAR(40) NOT NULL,
				`time` DATETIME NOT NULL,
				INDEX (`poll_id`, `answer_id`, `user_id`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array(
			"DROP TABLE `{pre}poll_votes`;",
			"DROP TABLE `{pre}poll_answers`;",
			"DROP TABLE `{pre}polls`;"
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
				"ALTER TABLE `{pre}polls` CHANGE `question` `title` VARCHAR(120) {charset} NOT NULL",
			)
		);
	}
}