<?php

namespace BplAdmin\Factory\Form;

use BplAdmin\Form\AppPermissionForm;
use BplAdmin\Service\ControllerGuardConfigManager;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AppPermissionFormFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return AdminController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $config = $container->get('Config');
        $roleMapperKey = $config['circlical']['user']['providers']['role'];
        $roleMapper = $container->get($roleMapperKey);

        $allRoles = $roleMapper->getAllRoles();
        
        foreach ($allRoles as $r) {
            $roleValueOptions[$r->getName()] = $r->getName();
            }
        return new AppPermissionForm(
                $container->get(ControllerGuardConfigManager::class),
                $roleValueOptions
        );
    }

}
