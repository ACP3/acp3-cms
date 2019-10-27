<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Component;

use ACP3\Core\Component\Dto\ComponentDataDto;
use ACP3\Core\Component\Exception\ComponentNotFoundException;
use MJS\TopSort\Implementations\StringSort;

class ComponentRegistry
{
    /**
     * @var ComponentDataDto[]
     */
    private static $components = [];

    /**
     * @var ComponentDataDto[]
     */
    private static $componentsTopSorted;

    /**
     * Adds a new component with its name and its filesystem path to the component registry.
     *
     * @param \ACP3\Core\Component\Dto\ComponentDataDto $component
     */
    public static function add(ComponentDataDto $component): void
    {
        self::$components[$component->getPath()] = $component;
    }

    /**
     * Return all currently registered components.
     *
     * @return ComponentDataDto[]
     */
    public static function getAllComponents(): array
    {
        return self::$components;
    }

    /**
     * @return ComponentDataDto[]
     *
     * @throws ComponentNotFoundException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public static function getAllComponentsTopSorted(): array
    {
        if (self::$componentsTopSorted !== null) {
            return self::$componentsTopSorted;
        }

        $topSort = new StringSort();

        $components = self::getAllComponents();

        foreach ($components as $component) {
            $dependencies = \array_map(static function (string $componentName) {
                $coreData = self::findComponentByName($componentName);

                return $coreData ? $coreData->getPath() : null;
            }, $component->getDependencies());

            $topSort->add($component->getPath(), $dependencies);
        }

        foreach ($topSort->sort() as $componentPath) {
            self::$componentsTopSorted[$componentPath] = $components[$componentPath];
        }

        return self::$componentsTopSorted;
    }

    /**
     * @param ComponentDataDto[] $components
     * @param string[]           $componentTypes
     *
     * @return ComponentDataDto[]
     */
    public static function filterComponentsByType(array $components, array $componentTypes): array
    {
        return \array_filter($components, static function (ComponentDataDto $component) use ($componentTypes) {
            return \in_array($component->getComponentType(), $componentTypes, true);
        });
    }

    private static function findComponentByName(string $componentName): ?ComponentDataDto
    {
        $componentName = \strtolower($componentName);
        $filteredComponents = \array_filter(self::$components, static function (ComponentDataDto $component) use ($componentName) {
            return $component->getName() === $componentName;
        });

        return \reset($filteredComponents) ?: null;
    }

    /**
     * Returns the filesystem path of the given component.
     * If the component isn't registered it throws an exception.
     *
     * @param string $componentName
     *
     * @return string
     *
     * @throws ComponentNotFoundException
     */
    public static function getPathByComponentName(string $componentName): string
    {
        $component = self::findComponentByName($componentName);

        if ($component === null) {
            throw new ComponentNotFoundException(
                \sprintf('Could not find the component with name "%s".', $componentName)
            );
        }

        return $component->getPath();
    }
}
