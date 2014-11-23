<?php

namespace ACP3\Modules\Menus;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\Menus
 */
class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'menus';
    const SCHEMA_VERSION = 33;

    /**
     * @inheritdoc
     */
    public function renameModule()
    {
        return array(
            31 => "UPDATE `{pre}modules` SET name = 'menus' WHERE name = 'menu_items';"
        );
    }

    /**
     * @inheritdoc
     */
    public function createTables()
    {
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

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return array(
            "DROP TABLE `{pre}menus`;",
            "DROP TABLE `{pre}menu_items`;"
        );
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function removeSettings()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return array(
            31 => array(
                "UPDATE `{pre}acl_resources` SET page = 'acp_create_item' WHERE module_id = " . $this->getModuleId() . " AND page = 'acp_create_block';",
                "UPDATE `{pre}acl_resources` SET page = 'acp_delete_item' WHERE module_id = " . $this->getModuleId() . " AND page = 'acp_delete_blocks';",
                "UPDATE `{pre}acl_resources` SET page = 'acp_edit_item' WHERE module_id = " . $this->getModuleId() . " AND page = 'acp_edit_block';",
                "DELETE  FROM `{pre}acl_resources` WHERE page = 'acp_list_blocks' AND module_id = " . $this->getModuleId() . ";",
                "RENAME TABLE `{pre}menu_items_blocks` TO `{pre}menus`"
            ),
            32 => array(
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
            ),
            33 => array(
                'UPDATE `{pre}acl_resources` SET controller = "items" WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_item";',
                'UPDATE `{pre}acl_resources` SET page = REPLACE(page, "_item", "") WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_item";',
                'UPDATE `{pre}acl_resources` SET controller = "items" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "order";',
            )
        );
    }

}