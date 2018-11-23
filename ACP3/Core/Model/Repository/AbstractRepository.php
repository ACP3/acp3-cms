<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Repository;

use ACP3\Core\Database\Connection;

abstract class AbstractRepository implements RepositoryInterface
{
    const TABLE_NAME = '';
    const PRIMARY_KEY_COLUMN = 'id';

    /**
     * @var \ACP3\Core\Database\Connection
     */
    protected $db;

    /**
     * @param \ACP3\Core\Database\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Executes the SQL insert statement.
     *
     * @param array $data
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function insert(array $data)
    {
        $this->db->getConnection()->insert(
            $this->getTableName(),
            $data
        );

        return (int) $this->db->getConnection()->lastInsertId();
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    public function getTableName(string $tableName = ''): string
    {
        return $this->db->getPrefixedTableName(!empty($tableName) ? $tableName : static::TABLE_NAME);
    }

    /**
     * Executes the SQL delete statement.
     *
     * @param int|array $entryId
     * @param string    $columnName
     *
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($entryId, string $columnName = 'id')
    {
        return $this->db->getConnection()->delete(
            $this->getTableName(),
            $this->getIdentifier($entryId, $columnName)
        );
    }

    /**
     * @param mixed  $entryId
     * @param string $columnName
     *
     * @return array
     */
    private function getIdentifier($entryId, string $columnName = self::PRIMARY_KEY_COLUMN)
    {
        return \is_array($entryId) === true ? $entryId : [$columnName => (int) $entryId];
    }

    /**
     * Executes the SQL update statement.
     *
     * @param array     $data
     * @param int|array $entryId
     *
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function update(array $data, $entryId)
    {
        return $this->db->getConnection()->update(
            $this->getTableName(),
            $data,
            $this->getIdentifier($entryId)
        );
    }

    /**
     * Build the SQL limit.
     *
     * @param int|null $limitStart
     * @param int|null $resultsPerPage
     *
     * @return string
     */
    protected function buildLimitStmt(?int $limitStart = null, ?int $resultsPerPage = null)
    {
        if ($limitStart !== null && $resultsPerPage !== null) {
            return " LIMIT {$limitStart},{$resultsPerPage}";
        } elseif ($limitStart !== null) {
            return " LIMIT {$limitStart}";
        }

        return '';
    }

    /**
     * @param int $entryId
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneById(int $entryId): array
    {
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE id = ?", [$entryId]);
    }
}
