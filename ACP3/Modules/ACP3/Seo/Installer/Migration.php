<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Installer;

class Migration implements \ACP3\Core\Installer\MigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [
            2 => [
                "DELETE FROM `{pre}settings` WHERE `module_id` = '{moduleId}' AND `name` LIKE 'seo_%';",
                "UPDATE `{pre}settings` SET `module_id` = '{moduleId}' WHERE `module_id` = (SELECT `id` FROM `{pre}modules` WHERE `name` = 'system') AND `name` LIKE 'seo_%';",
            ],
            3 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'admin', 'index', 'settings', '', 7);",
            ],
            4 => [
                "UPDATE `{pre}settings` SET `name` = SUBSTRING(`name`, 5) WHERE `module_id` = '{moduleId}' AND `name` LIKE 'seo_%';",
            ],
            5 => [
                'ALTER TABLE `{pre}seo` ENGINE = InnoDB',
            ],
            6 => [
                "DELETE FROM `{pre}settings` WHERE `module_id` = '{moduleId}' AND `name` = 'mod_rewrite';",
            ],
            7 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'admin', 'index', 'suggest', '', 4);",
            ],
            8 => [
                "DELETE FROM `{pre}settings` WHERE `module_id` = '{moduleId}' AND `name` = 'title';",
            ],
            9 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'sitemap_is_enabled', '0');",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'sitemap_save_mode', '2');",
            ],
            10 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'admin', 'index', 'sitemap', '', 7);",
            ],
            11 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'sitemap_separate', '0');",
            ],
            12 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'index_paginated_content', 'first');",
            ],
            13 => [
                'ALTER TABLE `{pre}seo` ADD COLUMN `title` VARCHAR(255) NOT NULL AFTER `alias`;',
            ],
            14 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = {moduleId} AND `area` = 'admin' AND `controller` = 'index' AND `page` = 'create';",
                "UPDATE `{pre}acl_resources` SET `page` = 'manage' WHERE `module_id` = {moduleId} AND `area` = 'admin' AND `controller` = 'index' AND `page` = 'edit';",
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function renameModule()
    {
        return [];
    }
}
