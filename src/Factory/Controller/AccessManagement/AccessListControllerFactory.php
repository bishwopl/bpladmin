<?php

namespace BplAdmin\Factory\Controller\AccessManagement;

use BplAdmin\Controller\AccessManagement\AccessListController;
use BplAdmin\Service\ControllerGuardConfigManager;
use BplAdmin\Service\ResourceGuardConfigManager;
use BplAdmin\Form\AppPermissionForm;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use CirclicalUser\Mapper\RoleMapper;

class AccessListControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return AdminController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $config = $container->get('Config');
        $roleMapperKey = $config['circlical']['user']['providers']['role'] ?? RoleMapper::class;
        $roleMapper = $container->get($roleMapperKey);

        $allRoles = $roleMapper->getAllRoles();

        foreach ($allRoles as $r) {
            $roleValueOptions[$r->getId()] = $r->getName();
        }
        return new AccessListController(
                $container->get(ControllerGuardConfigManager::class),
                $container->get(ResourceGuardConfigManager::class),
                $container->get(AppPermissionForm::class),
                $roleValueOptions
        );
    }
}
