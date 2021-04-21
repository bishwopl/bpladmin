<?php

namespace BplAdmin\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use BplAdmin\Service\ControllerGuardConfigManager;
use BplAdmin\Form\Fieldset\ControllerPermissionFieldset;
use BplAdmin\Form\Fieldset\ActionPermissionFieldset;

class AppPermissionForm extends Form {

    /**
     * @var \BplAdmin\Service\ControllerGuardConfigManager 
     */
    protected $appPermissionService;

    /**
     * @var array 
     */
    protected $roleNames;

    public function __construct(ControllerGuardConfigManager $c, $roleNames) {
        parent::__construct('app_permission', []);
        $this->appPermissionService = $c;
        $this->roleNames = $roleNames;
        $this->setAttributes([
            'class' => 'form form-horizontal'
        ]);
        $this->init();
    }

    public function init(): void {
        $controllerNames = $this->appPermissionService->getControllerNames();
        
        foreach ($controllerNames as $controllerName) {
            $actionNames = $this->appPermissionService->getActionNames($controllerName);
            $this->add(
                    new ControllerPermissionFieldset($controllerName, $this->roleNames, $actionNames)
            );
        }

        $csrf = new Element\Csrf('csrf');
        $csrf->getCsrfValidator()->setTimeout(600);
        $this->add($csrf);

        $submitElement = new Element\Button('submit');
        $submitElement
                ->setLabel('Submit')
                ->setAttributes([
                    'type' => 'submit',
                    'class' => 'btn btn-success'
        ]);
        $this->add($submitElement);
    }

}
