<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class RawColumnType implements ColumnTypeStrategyInterface
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function doEscape($value)
    {
        return $value;
    }
}
