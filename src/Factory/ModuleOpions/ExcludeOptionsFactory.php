<?php

namespace BplAdmin\Factory\ModuleOpions;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use BplAdmin\ModuleOpions\ExcludeOptions;

class ExcludeOptionsFactory implements FactoryInterface {

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
        return new ExcludeOptions(
            isset($config['bpl_admin']) && isset($config['bpl_admin']['exclude']) ? $config['bpl_admin']['exclude'] : []
        );
    }

}