<?php

namespace BplAdmin\Factory\Controller\UserManagement;

use BplAdmin\Controller\UserManagement\DeleteController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use CirclicalUser\Mapper\UserMapper;
use CirclicalUser\Mapper\AuthenticationMapper;

class DeleteControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return CredentialController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $config = $container->get('Config');
        $userMapperKey = $config['circlical']['user']['providers']['user'] ?? UserMapper::class;
        $authMapperKey = $config['circlical']['user']['providers']['auth'] ?? AuthenticationMapper::class;
        return new DeleteController(
                $container->get($userMapperKey),
                $container->get($authMapperKey)
        );
    }
}
