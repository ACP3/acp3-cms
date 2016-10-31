<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\Event;


use Symfony\Component\EventDispatcher\Event;

class ModelSaveEvent extends Event
{
    /**
     * @var string
     */
    private $moduleName;
    /**
     * @var array
     */
    private $filteredData;
    /**
     * @var int|null
     */
    private $entryId;
    /**
     * @var array
     */
    private $rawData;

    /**
     * ModelSaveEvent constructor.
     * @param string $moduleName
     * @param array $filteredData
     * @param array $rawData
     * @param int|null|array $entryId
     */
    public function __construct($moduleName, array $filteredData, array $rawData, $entryId)
    {
        $this->moduleName = $moduleName;
        $this->filteredData = $filteredData;
        $this->rawData = $rawData;
        $this->entryId = $entryId;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->filteredData;
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @return int|null|array
     */
    public function getEntryId()
    {
        return $this->entryId;
    }

    /**
     * @return bool
     */
    public function isDeleteStatement()
    {
        return count($this->filteredData) === 0 && is_array($this->entryId);
    }
}
