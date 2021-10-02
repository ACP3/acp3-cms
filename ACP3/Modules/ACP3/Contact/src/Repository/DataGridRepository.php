<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Repository;

use ACP3\Core\DataGrid\Repository\AbstractDataGridRepository;

class DataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = ContactsRepository::TABLE_NAME;
}