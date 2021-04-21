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
            'type' => Element\Hidden::class,
            'name' => 'controllerName',
            'options' => [
                'label' => 'Controller Name',
            ],
            'attributes' => [
                'value' => $this->controllerName,
                'readOnly' => true,
                'class' => 'form-control input',
            ],
        ]);

        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'allowAllByDefault',
            'options' => [
                'label' => 'Allow all roles',
                'class' => 'form-control input'
            ],
        ]);

        $roleSelect = new Element\Select('defaultAllowedRoles');
        $roleSelect->setOptions([
            'label' => 'Select Default Allowed Roles',
            'empty_option' => '--Select Allowed Role--',
        ]);
        $roleSelect->setValueOptions($this->roleNames);
        $roleSelect->setAttributes([
            'multiple' => true,
            'class' => 'form-control input',
        ]);

        $this->add($roleSelect);

        $actionsFieldSet = new Fieldset('actions');
        
        foreach ($this->actionNames as $a) {
            $fieldset = new ActionPermissionFieldset($a, $this->roleNames);
            $actionsFieldSet->add($fieldset);
        }
        $this->add($actionsFieldSet);
    }

    public function getInputFilterSpecification(): array {
        return [
            'allowAllRoles' => [
                'required' => false
            ],
            'defaultAllowedRoles' => [
                'required' => false
            ],
        ];
    }

}
