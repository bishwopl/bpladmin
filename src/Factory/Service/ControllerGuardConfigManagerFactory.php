<?php

declare(strict_types=1);

namespace BplAdmin\Factory\Service;

use BplAdmin\Service\ControllerGuardConfigManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ControllerGuardConfigManagerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return ControllerGuardConfigManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $config = $container->get('Config');
        $roleMapperKey = $config['circlical']['user']['providers']['role'];

        return new ControllerGuardConfigManager(
                $container->get(\BplAdmin\ModuleOpions\AccessOptions::class),
                $container->get($roleMapperKey),
                $config
        );
    }

}
