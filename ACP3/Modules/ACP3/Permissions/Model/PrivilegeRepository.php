<?php
namespace ACP3\Modules\ACP3\Permissions\Model;

use ACP3\Core;

/**
 * Class PrivilegeRepository
 * @package ACP3\Modules\ACP3\Permissions\Model
 */
class PrivilegeRepository extends Core\Model
{
    const TABLE_NAME = 'acl_privileges';

    /**
     * @param int $id
     *
     * @return bool
     */
    public function privilegeExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id', ['id' => $id]) > 0;
    }

    /**
     * @return array
     */
    public function getAllPrivilegeIds()
    {
        return $this->db->fetchAll('SELECT id FROM ' . $this->getTableName());
    }

    /**
     * @return array
     */
    public function getAllPrivileges()
    {
        return $this->db->fetchAll('SELECT id, `key`, description FROM ' . $this->getTableName() . ' ORDER BY `key` ASC');
    }
}
