<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Model\Repository;

use ACP3\Core;
use ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository;

class CategoryRepository extends Core\Model\Repository\AbstractRepository
{
    const TABLE_NAME = 'categories';

    /**
     * @param int $categoryId
     *
     * @return bool
     */
    public function resultExists($categoryId)
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = ?", [$categoryId]) > 0;
    }

    /**
     * @param string $title
     * @param int    $moduleId
     * @param int    $categoryId
     *
     * @return bool
     */
    public function resultIsDuplicate($title, $moduleId, $categoryId)
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE title = ? AND module_id = ? AND id != ?", [$title, $moduleId, $categoryId]) > 0;
    }

    /**
     * @param int $categoryId
     *
     * @return array
     */
    public function getOneById($categoryId)
    {
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE id = ?", [$categoryId]);
    }

    /**
     * @param int $categoryId
     *
     * @return string
     */
    public function getTitleById($categoryId)
    {
        return $this->db->fetchColumn("SELECT `title` FROM {$this->getTableName()} WHERE id = ?", [$categoryId]);
    }

    /**
     * @param string $moduleName
     *
     * @return array
     */
    public function getAllByModuleName($moduleName)
    {
        return $this->db->fetchAll(
            'SELECT c.* FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE m.name = ? ORDER BY c.title ASC',
            [$moduleName]
        );
    }

    /**
     * @param int $categoryId
     *
     * @return string
     */
    public function getModuleNameFromCategoryId($categoryId)
    {
        return $this->db->fetchColumn(
            'SELECT m.name FROM ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m JOIN ' . $this->getTableName() . ' AS c ON(m.id = c.module_id) WHERE c.id = ?',
            [$categoryId]
        );
    }

    /**
     * @param int $categoryId
     *
     * @return int
     */
    public function getModuleIdByCategoryId($categoryId)
    {
        return (int) $this->db->fetchColumn("SELECT `module_id` FROM {$this->getTableName()} WHERE `id` = ?", [$categoryId]);
    }

    /**
     * @param int $categoryId
     *
     * @return array
     */
    public function getCategoryDeleteInfosById($categoryId)
    {
        return $this->db->fetchAssoc(
            'SELECT c.picture, m.name AS module FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE c.id = ?',
            [$categoryId]
        );
    }

    /**
     * @param string $title
     * @param string $moduleName
     *
     * @return array
     */
    public function getOneByTitleAndModule($title, $moduleName)
    {
        return $this->db->fetchAssoc(
            'SELECT c.* FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE c.title = ? AND m.name = ?',
            [$title, $moduleName]
        );
    }
}