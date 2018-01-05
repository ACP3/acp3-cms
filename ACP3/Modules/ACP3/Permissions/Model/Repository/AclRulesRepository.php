<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model\Repository;

use ACP3\Core;

class AclRulesRepository extends Core\Model\Repository\AbstractRepository
{
    const TABLE_NAME = 'acl_rules';

    /**
     * @param array $roles
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllRulesByRoleIds(array $roles)
    {
        return $this->db->getConnection()->executeQuery(
            'SELECT ru.role_id, ru.privilege_id, ru.permission, ru.module_id, m.name AS module_name, p.key, p.description FROM ' . $this->getTableName() . ' AS ru JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository::TABLE_NAME) . ' AS m ON (ru.module_id = m.id) JOIN ' . $this->getTableName(AclPrivilegesRepository::TABLE_NAME) . " AS p ON(ru.privilege_id = p.id) JOIN {$this->getTableName(AclRolesRepository::TABLE_NAME)} AS ro ON(ro.id = ru.role_id) WHERE m.active = 1 AND ro.id IN(?)",
            [$roles],
            [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
        )->fetchAll();
    }
}
