<?php

namespace BplAdmin\Form\Fieldset;

use Laminas\Form\Fieldset;
use Laminas\Form\Element;
use Laminas\InputFilter\InputFilterProviderInterface;

class ControllerPermissionFieldset extends Fieldset implements InputFilterProviderInterface {

    /**
     * @var string 
     */
    protected $controllerName;

    /**
     * @var array 
     */
    protected $roleNames;

    /**
     * @var array 
     */
    protected $actionNames;

    /**
     * @param string $controllerName
     * @param array $roleNames 
     * @param array $actionNames
     * @return void
     */
    public function __construct(string $controllerName, array $roleNames, array $actionNames) {
        parent::__construct($controllerName, []);
        $this->controllerName = $controllerName;
        $this->roleNames = $roleNames;
        $this->actionNames = $actionNames;
        $this->init();
    }

    public function init(): void {
        $this->add([
            'type' => Element\Text::class,
            'name' => 'controllerName',
            'options' => [
                'label' => 'Controller Name',
            ],
        ]);

        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'allowAllRoles',
            'options' => [
                'label' => 'Default Allowed Roles',
            ],
        ]);

        $roleSelect = new Element\Select('defaultAllowedRoles');
        $roleSelect->setOptions([
            'label' => 'Select Allowed Roles',
            'empty_option' => '--Select Allowed Role--',
        ]);
        $roleSelect->setValueOptions($this->roleNames);
        $roleSelect->setAttributes([
            'multiple' => true,
            'class' => 'form-control input',
            'style' => 'min-height: 200px;'
        ]);

        $this->add($roleSelect);

        $this->add([
            'type' => Element\Collection::class,
            'name' => 'actionPermissions',
            'options' => [
                'label' => 'Action Permissions',
                'count' => sizeof($this->actionNames),
                'should_create_template' => true,
                'target_element' => new ActionPermissionFieldset($this->roleNames)
            ],
        ]);

        /** foreach ($this->actionNames as $a) {
          $actionFieldset = new ActionPermissionFieldset($a, $this->roleNames);
          $this->add($actionFieldset);
          } */
    }

    public function getInputFilterSpecification(): array {
        return [];
    }

}
