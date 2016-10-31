<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class IntegerColumnType implements ColumnTypeStrategyInterface
{
    /**
     * @param mixed $value
     * @return int
     */
    public function doEscape($value)
    {
        return intval($value);
    }
}