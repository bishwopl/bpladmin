<?php

namespace BplAdmin\Factory\Controller\UserManagement;

use BplAdmin\Controller\UserManagement\AssignRoleController;
use BplAdmin\Form\AssignRoleForm;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use CirclicalUser\Mapper\UserMapper;
use CirclicalUser\Mapper\RoleMapper;

class AssignRoleControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return UserManagementController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $config = $container->get('Config');
        $userMapperKey = $config['circlical']['user']['providers']['user']??UserMapper::class;
        $roleMapperKey = $config['circlical']['user']['providers']['role']??RoleMapper::class;
        $roleMapper = $container->get($roleMapperKey);
        
        $allRoles = $roleMapper->getAllRoles();
        
        foreach($allRoles as $r){
            $roleValueOptions[$r->getId()] = $r->getName();
        }
        $form = new AssignRoleForm($roleValueOptions, 'roles');

        return new AssignRoleController(
                $form,
                $container->get($userMapperKey),
                $roleMapper,
        );
    }

}
