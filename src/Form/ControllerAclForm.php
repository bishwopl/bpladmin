<?php

namespace BplAdmin\Form;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use BplAdmin\Service\ControllerGuardConfigManager;

class ControllerAclForm extends Form implements InputFilterProviderInterface {

    public function __construct(ControllerGuardConfigManager $aclManager) {
        parent::__construct('controller-acl-form', []);
        $controllerNames = $aclManager->getControllerNames();
        foreach ($controllerNames as $c){
            $actionName = $aclManager->getActionNames($c);
            foreach ($actionName as $a){
                
            }
        }
    }

    public function getInputFilterSpecification(): array {
        return [
        ];
    }

}
