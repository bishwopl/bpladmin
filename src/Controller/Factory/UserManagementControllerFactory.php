<?php

namespace BplAdmin\Controller\Factory;

use BplAdmin\Controller\UserManagementController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UserManagementControllerFactory implements FactoryInterface
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
        $authMapperKey = $config['circlical']['user']['providers']['auth'];
        $authenticationMapper = $container->get($authMapperKey);
        $moduleOptions = $container->get(\BplUser\Options\ModuleOptions::class);
        $registrationForm = $container->get($moduleOptions->getRegistrationFormFactory());
        $profileForm = $container->get($moduleOptions->getChangeProfileFormFactory());
        
        if ($moduleOptions->getUseRegistrationFormCaptcha()) {
            $registrationForm->remove('captcha');
        }
        
        foreach ($registrationForm as $element){
            if($element instanceof \Laminas\Form\Element && $element->getAttribute('type')!=='submit'){
                $element->setAttribute("class", "form-control input");
            }
        }
        
        foreach ($profileForm as $element){
            if($element instanceof \Laminas\Form\Element && $element->getAttribute('type')!=='submit'){
                $element->setAttribute("class", "form-control input");
            }
        }
        
        return new UserManagementController(
            $container->get(\BplAdmin\ModuleOpions\CrudOptions::class),
            $container->get(\BplAdmin\Service\UserService::class),
            $registrationForm,
            $profileForm,
            new $userEntity,
            $authenticationMapper
        );
    }
}

