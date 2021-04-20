<?php

namespace BplAdmin\Factory\Controller\UserManagement;

use BplAdmin\Controller\UserManagement\RegisterController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RegisterControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return UserManagementController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $userEntity = $config['circlical']['user']['doctrine']['entity'];
        $moduleOptions = $container->get(\BplUser\Options\ModuleOptions::class);
        $registrationForm = $container->get($moduleOptions->getRegistrationFormFactory());
        
        if ($moduleOptions->getUseRegistrationFormCaptcha()) {
            $registrationForm->remove('captcha');
        }
        
        foreach ($registrationForm as $element){
            if($element instanceof \Laminas\Form\Element && $element->getAttribute('type')!=='submit'){
                $element->setAttribute("class", "form-control input");
            }
        }
        
        return new RegisterController(
            $container->get(\BplUser\Service\BplUserService::class),
            $container->get(\CirclicalUser\Service\AuthenticationService::class),
            $registrationForm,
            new $userEntity
        );
    }
}

