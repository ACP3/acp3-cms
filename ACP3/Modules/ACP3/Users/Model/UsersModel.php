<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Model;


use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor\ColumnTypes;
use ACP3\Modules\ACP3\Users\Installer\Schema;

class UsersModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'super_user' => ColumnTypes::COLUMN_TYPE_INT,
            'nickname' => ColumnTypes::COLUMN_TYPE_TEXT,
            'pwd' => ColumnTypes::COLUMN_TYPE_RAW,
            'pwd_salt' => ColumnTypes::COLUMN_TYPE_RAW,
            'realname' => ColumnTypes::COLUMN_TYPE_TEXT,
            'gender' => ColumnTypes::COLUMN_TYPE_INT,
            'birthday' => ColumnTypes::COLUMN_TYPE_DATETIME,
            'birthday_display' => ColumnTypes::COLUMN_TYPE_BOOLEAN,
            'mail' => ColumnTypes::COLUMN_TYPE_TEXT,
            'mail_display' => ColumnTypes::COLUMN_TYPE_BOOLEAN,
            'website' => ColumnTypes::COLUMN_TYPE_TEXT,
            'icq' => ColumnTypes::COLUMN_TYPE_TEXT,
            'skype' => ColumnTypes::COLUMN_TYPE_TEXT,
            'street' => ColumnTypes::COLUMN_TYPE_TEXT,
            'house_number' => ColumnTypes::COLUMN_TYPE_TEXT,
            'zip' => ColumnTypes::COLUMN_TYPE_TEXT,
            'city' => ColumnTypes::COLUMN_TYPE_TEXT,
            'address_display' => ColumnTypes::COLUMN_TYPE_BOOLEAN,
            'country' => ColumnTypes::COLUMN_TYPE_TEXT,
            'country_display' => ColumnTypes::COLUMN_TYPE_BOOLEAN,
            'date_format_long' => ColumnTypes::COLUMN_TYPE_TEXT,
            'date_format_short' => ColumnTypes::COLUMN_TYPE_TEXT,
            'time_zone' => ColumnTypes::COLUMN_TYPE_RAW,
            'language' => ColumnTypes::COLUMN_TYPE_RAW,
            'registration_date' => ColumnTypes::COLUMN_TYPE_DATETIME
        ];
    }
}
