<?php

namespace BplAdmin\Factory\ModuleOpions;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use BplAdmin\ModuleOpions\AccessOptions;

class AccessOptionsFactory implements FactoryInterface {

    /**
     * Create ModuleOptions Service
     * 
     * @param ContainerInterface $container
     * @param type $requestedName
     * @param mixed $options
     * @return ModuleOptions
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null) {
        $config = $container->get('Config');
        return new AccessOptions(
            isset($config['bpl_admin']) && isset($config['bpl_admin']['acl']) ? $config['bpl_admin']['acl'] : ''
        );
    }

}