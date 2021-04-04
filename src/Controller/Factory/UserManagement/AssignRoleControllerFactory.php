<?php

namespace BplAdmin\Controller\Factory\UserManagement;

use BplAdmin\Controller\UserManagement\AssignRoleController;
use BplAdmin\Form\AssignRoleForm;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AssignRoleControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return UserManagementController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $config = $container->get('Config');
        $em = $container->get(\Doctrine\ORM\EntityManager::class);
        $userMapperKey = $config['circlical']['user']['providers']['user'];
        $roleMapperKey = $config['circlical']['user']['providers']['role'];
        $form = new AssignRoleForm($em, 'roles');

        return new AssignRoleController(
                $form,
                $container->get($userMapperKey),
                $container->get($roleMapperKey),
        );
    }

}
