<?php

namespace BplAdmin\ModuleOpions;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class CrudOptionsFactory implements FactoryInterface {

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
        return new \BplAdmin\ModuleOpions\CrudOptions(
            isset($config['bpl_admin']) && isset($config['bpl_admin']['crud']) ? $config['bpl_admin']['crud'] : []
        );
    }

}