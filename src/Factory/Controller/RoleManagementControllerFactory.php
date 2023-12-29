<?php

namespace BplAdmin\Factory\Controller;

use BplAdmin\Controller\RoleManagementController;
use BplAdmin\Form\RoleForm;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use CirclicalUser\Mapper\RoleMapper;

class RoleManagementControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return RoleManagementController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $roleValueOptions = [];
        $config = $container->get('Config');
        $roleMapperKey = $config['circlical']['user']['providers']['role'] ?? RoleMapper::class;
        $moduleOptions = $container->get(\BplAdmin\ModuleOpions\CrudOptions::class);
        $roleMapper = $container->get($roleMapperKey);
        $allRoles = $roleMapper->getAllRoles();

        foreach ($allRoles as $r) {
            $roleValueOptions[$r->getName()] = $r->getName();
        }

        return new RoleManagementController(
                $moduleOptions,
                $roleMapper,
                new RoleForm($roleValueOptions, 'role')
        );
    }
}
