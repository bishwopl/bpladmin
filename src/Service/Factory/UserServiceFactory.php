<?php

namespace BplAdmin\Service\Factory;

use BplAdmin\Service\UserService;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UserServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return UserService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $moduleOptions = $container->get(\BplUser\Options\ModuleOptions::class);
        $userEntity = $config['circlical']['user']['doctrine']['entity'];
        return new UserService(
            $container->get(\Doctrine\ORM\EntityManager::class),
            $container->get($moduleOptions->getChangeProfileFormFactory()),
            $userEntity
        );
    }
}
