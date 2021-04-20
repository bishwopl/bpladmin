<?php

namespace BplAdmin\Factory\Controller\UserManagement;

use BplAdmin\Controller\UserManagement\ListController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ListControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return ListController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $authMapperKey = $config['circlical']['user']['providers']['auth'];
        $userMapperKey = $config['circlical']['user']['providers']['user'];
        
        return new ListController(
            $container->get(\BplAdmin\ModuleOpions\CrudOptions::class),
            $container->get($userMapperKey),
            $container->get($authMapperKey)
        );
    }
}
