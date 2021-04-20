<?php

namespace BplAdmin\Factory\Controller\UserManagement;

use BplAdmin\Controller\UserManagement\CredentialController;
use BplAdmin\Form\ChangeCredentialForm;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CredentialControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return CredentialController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $config = $container->get('Config');
        $userMapperKey = $config['circlical']['user']['providers']['user'];
        return new CredentialController(
                $container->get($userMapperKey),
                new ChangeCredentialForm('cred-form')
        );
    }

}
