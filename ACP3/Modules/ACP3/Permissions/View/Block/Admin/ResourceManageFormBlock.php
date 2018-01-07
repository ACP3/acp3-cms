<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\View\Block\Admin;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Modules\Modules;
use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Permissions\Model\Repository\AclPrivilegesRepository;
use ACP3\Modules\ACP3\Permissions\Model\Repository\AclResourcesRepository;

class ResourceManageFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var AclPrivilegesRepository
     */
    private $privilegeRepository;

    /**
     * ResourceFormBlock constructor.
     *
     * @param FormBlockContext        $context
     * @param AclResourcesRepository  $aclResourcesRepository
     * @param Modules                 $modules
     * @param AclPrivilegesRepository $privilegeRepository
     */
    public function __construct(
        FormBlockContext $context,
        AclResourcesRepository $aclResourcesRepository,
        Modules $modules,
        AclPrivilegesRepository $privilegeRepository
    ) {
        parent::__construct($context, $aclResourcesRepository);

        $this->modules = $modules;
        $this->privilegeRepository = $privilegeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $resource = $this->getData();

        $this->breadcrumb->setLastStepReplacement(
            $this->translator->t('permissions', !$this->getId() ? 'admin_resources_create' : 'admin_resources_edit')
        );

        if (!empty($resource['page'])) {
            $resource['resource'] = $resource['page'];
            unset($resource['page']);
        }

        return [
            'modules' => $this->fetchActiveModules($resource['module_name']),
            'areas' => $this->fetchAreas($resource['area']),
            'privileges' => $this->fetchPrivileges($resource['privilege_id']),
            'form' => \array_merge(
                $resource,
                $this->getRequestData()
            ),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }

    /**
     * @param int $privilegeId
     *
     * @return array
     */
    private function fetchPrivileges(int $privilegeId): array
    {
        $privileges = $this->privilegeRepository->getAllPrivileges();
        $cPrivileges = \count($privileges);
        for ($i = 0; $i < $cPrivileges; ++$i) {
            $privileges[$i]['selected'] = $this->forms->selectEntry(
                'privileges',
                $privileges[$i]['id'],
                $privilegeId
            );
        }

        return $privileges;
    }

    /**
     * @param string $currentModule
     *
     * @return array
     */
    private function fetchActiveModules(string $currentModule = ''): array
    {
        $modules = $this->modules->getActiveModules();
        foreach ($modules as $row) {
            $modules[$row['name']]['selected'] = $this->forms->selectEntry(
                'modules',
                $row['dir'],
                \ucfirst(\trim($currentModule))
            );
        }

        return $modules;
    }

    /**
     * @param string $currentArea
     *
     * @return array
     */
    private function fetchAreas(string $currentArea = ''): array
    {
        $areas = \array_values(AreaEnum::getAreas());

        return $this->forms->choicesGenerator('area', \array_combine($areas, $areas), $currentArea);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [
            'id' => 0,
            'privilege_id' => 0,
            'module_name' => '',
            'area' => '',
            'resource' => '',
            'controller' => '',
        ];
    }
}
