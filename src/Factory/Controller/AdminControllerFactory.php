<?php

namespace BplAdmin\Factory\Controller;

use BplAdmin\Controller\AdminController;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AdminControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return AdminController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AdminController();
    }
}

