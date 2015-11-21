<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;
use ACP3\Core\Date;
use ACP3\Core\Helpers\Formatter\DateRange;

/**
 * Class DateColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class DateColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Formatter\DateRange
     */
    protected $dateRangeHelper;

    /**
     * @param \ACP3\Core\Date                        $date
     * @param \ACP3\Core\Helpers\Formatter\DateRange $dateRangeHelper
     */
    public function __construct(
        Date $date,
        DateRange $dateRangeHelper
    )
    {
        $this->date = $date;
        $this->dateRangeHelper = $dateRangeHelper;
    }

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $value = $this->getValue($column, $dbResultRow);

        if ($value !== null && $value !== $this->getDefaultValue($column)) {
            $field = $this->getFirstDbField($column);
            $column['attribute'] += [
                'data-order' => $this->date->format($dbResultRow[$field], 'U')
            ];
        }

        return $this->render(
            $column,
            $value
        );
    }

    /**
     * @inheritdoc
     */
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        return isset($dbResultRow[$field]) ? $this->dateRangeHelper->formatTimeRange($dbResultRow[$field]) : null;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'date';
    }
}