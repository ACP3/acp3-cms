<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Model\Repository\AbstractRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractModel
{
    const EVENT_PREFIX = '';

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var AbstractRepository
     */
    protected $repository;
    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    /**
     * AbstractModel constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param DataProcessor            $dataProcessor
     * @param AbstractRepository       $repository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        AbstractRepository $repository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $repository;
        $this->dataProcessor = $dataProcessor;
    }

    protected function getDataProcessor(): DataProcessor
    {
        return $this->dataProcessor;
    }

    /**
     * @param array    $rawData
     * @param int|null $entryId
     *
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function save(array $rawData, $entryId = null)
    {
        $filteredData = $this->prepareData($rawData, $entryId);

        $isNewEntry = $entryId === null;
        $hasDataChanges = $this->hasDataChanges($filteredData, $entryId);
        $event = $this->createModelSaveEvent($entryId, $isNewEntry, $hasDataChanges, $filteredData, $rawData);

        $this->dispatchBeforeSaveEvent($this->repository, $event);

        if ($entryId === null) {
            $result = $this->repository->insert($filteredData);

            if ($result !== false) {
                $entryId = $result;
            }
        } else {
            $result = $hasDataChanges ? $this->repository->update($filteredData, $entryId) : 1;
        }

        $event = $this->createModelSaveEvent($entryId, $isNewEntry, $hasDataChanges, $filteredData, $rawData);
        $this->dispatchAfterSaveEvent(
            $this->repository,
            $event
        );

        return $result;
    }

    /**
     * @param array    $filteredData
     * @param int|null $entryId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function hasDataChanges(array $filteredData, ?int $entryId): bool
    {
        if ($entryId === null) {
            return true;
        }

        $result = $this->getOneById($entryId);

        if ($this instanceof UpdatedAtAwareModelInterface) {
            unset($result['updated_at']);
        }

        foreach ($result as $column => $value) {
            if (isset($filteredData[$column]) && $filteredData[$column] != $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \ACP3\Core\Model\Repository\AbstractRepository $repository
     * @param \ACP3\Core\Model\Event\ModelSaveEvent          $event
     */
    protected function dispatchBeforeSaveEvent(
        AbstractRepository $repository,
        ModelSaveEvent $event
    ) {
        $this->dispatchEvent(
            'core.model.before_save',
            $event
        );
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.before_save',
            $event
        );
    }

    /**
     * @param string                                $eventName
     * @param \ACP3\Core\Model\Event\ModelSaveEvent $event
     */
    protected function dispatchEvent(
        string $eventName,
        ModelSaveEvent $event)
    {
        $this->eventDispatcher->dispatch(
            $event,
            $eventName
        );
    }

    protected function createModelSaveEvent(
        $entryId,
        bool $isNewEntry,
        bool $hasDataChanges,
        array $filteredData = [],
        array $rawData = []
    ): ModelSaveEvent {
        return new ModelSaveEvent(
            static::EVENT_PREFIX,
            $filteredData,
            $rawData,
            $entryId,
            $isNewEntry,
            $hasDataChanges,
            $this->repository::TABLE_NAME
        );
    }

    /**
     * @param array    $rawData
     * @param int|null $entryId
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function prepareData(array $rawData, ?int $entryId)
    {
        if ($entryId !== null) {
            $rawData = \array_merge($this->getOneById($entryId), $rawData);
        }

        return $this->dataProcessor->escape($rawData, $this->getAllowedColumns());
    }

    /**
     * @return array
     */
    abstract protected function getAllowedColumns();

    /**
     * @param \ACP3\Core\Model\Repository\AbstractRepository $repository
     * @param \ACP3\Core\Model\Event\ModelSaveEvent          $event
     */
    protected function dispatchAfterSaveEvent(
        AbstractRepository $repository,
        ModelSaveEvent $event
    ) {
        $this->dispatchEvent(
            'core.model.after_save',
            $event
        );
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.after_save',
            $event
        );
    }

    /**
     * @param int|array $entryId
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function delete($entryId)
    {
        $repository = $this->repository;

        if (!\is_array($entryId)) {
            $entryId = [$entryId];
        }

        $event = $this->createModelSaveEvent($entryId, false, true);

        $this->dispatchEvent('core.model.before_delete', $event);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.before_delete',
            $event
        );

        $affectedRows = 0;
        foreach ($entryId as $item) {
            $affectedRows += (int) $this->repository->delete($item);
        }

        $this->dispatchEvent('core.model.after_delete', $event);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.after_delete',
            $event
        );

        return $affectedRows;
    }

    /**
     * @param int $entryId
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneById(int $entryId)
    {
        return $this->repository->getOneById($entryId);
    }

    /**
     * @return AbstractRepository
     */
    protected function getRepository()
    {
        return $this->repository;
    }
}