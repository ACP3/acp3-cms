<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Model\Repository;

use ACP3\Core\Model\Repository\AbstractRepository;
use ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface;

class ModulesRepository extends AbstractRepository implements ModuleAwareRepositoryInterface
{
    const TABLE_NAME = 'modules';

    /**
     * {@inheritdoc}
     */
    public function getModuleId(string $moduleName): int
    {
        return $this->db->fetchColumn(
            'SELECT `id` FROM ' . $this->getTableName() . ' WHERE `name` = ?',
            [$moduleName]
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getModuleSchemaVersion(string $moduleName): int
    {
        return $this->db->fetchColumn(
            'SELECT `version` FROM ' . $this->getTableName() . ' WHERE `name` = ?',
            [$moduleName]
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function moduleExists(string $moduleName): bool
    {
        return $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `name` = ?',
            [$moduleName]
        ) > 0;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getInfoByModuleName(string $moduleName): array
    {
        return $this->db->fetchAssoc(
            'SELECT `id`, `version`, `active` FROM ' . $this->getTableName() . ' WHERE `name` = ?',
            [$moduleName]
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getModuleNameById(int $moduleId): string
    {
        return $this->db->fetchColumn(
            'SELECT `name` FROM ' . $this->getTableName() . ' WHERE `id` = ?',
            [$moduleId]
        );
    }
}