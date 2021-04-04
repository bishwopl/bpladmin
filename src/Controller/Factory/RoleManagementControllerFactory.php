<?php

namespace BplAdmin\Controller\Factory;

use BplAdmin\Controller\RoleManagementController;
use BplAdmin\Service\AclRolesService;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RoleManagementControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return RoleManagementController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $em = $container->get(\Doctrine\ORM\EntityManager::class);
        $modoleOptions = $container->get(\BplAdmin\ModuleOpions\CrudOptions::class);
        return new RoleManagementController(
                $modoleOptions,
                new AclRolesService($em)
        );
    }

}
