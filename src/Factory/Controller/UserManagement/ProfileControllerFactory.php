<?php

namespace BplAdmin\Factory\Controller\UserManagement;

use BplAdmin\Controller\UserManagement\ProfileController;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProfileControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return ProfileController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $userMapperKey = $config['circlical']['user']['providers']['user'];
        $moduleOptions = $container->get(\BplUser\Options\ModuleOptions::class);
        
        $changeProfileForm = $container->get($moduleOptions->getChangeProfileFormFactory());
        $hasEmailField = false;
        
        foreach ($changeProfileForm as $element){
            if($element instanceof \Laminas\Form\Element && $element->getAttribute('type')!=='submit'){
                $element->setAttribute("class", "form-control input");
            }
            if($element->getName() == 'email'){
                $hasEmailField = true;
            }
        }
        
        if(!$hasEmailField){
            $email = new \Laminas\Form\Element\Email('email');
            $email->setLabel('Email Address');
            $email->setAttribute("class", "form-control input");
            $email->setEmailValidator(new \Laminas\Validator\EmailAddress());
            $changeProfileForm->add($email,['priority' => 1000]);
        }
        
        return new ProfileController(
            $container->get($userMapperKey),
            $changeProfileForm
        );
    }
}
