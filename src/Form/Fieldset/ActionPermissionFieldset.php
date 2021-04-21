<?php

namespace BplAdmin\Form\Fieldset;

use Laminas\Form\Fieldset;
use Laminas\Form\Element;
use Laminas\InputFilter\InputFilterProviderInterface;

class ActionPermissionFieldset extends Fieldset implements InputFilterProviderInterface {
    
    /**
     * @var string
     */
    protected $actionName;

    /**
     * @var array 
     */
    protected $roleNames;

    public function __construct(string $actionName, array $roleNames) {
        $this->roleNames = $roleNames;
        $this->actionName = $actionName;
        parent::__construct($actionName, []);
        $this->init();
    }
    
    public function init() {
        $this->add([
            'type' => Element\Hidden::class,
            'name' => 'actionName',
            'options' => [
                'label' => 'Action Name',
            ],
            'attributes' => [
                'value' => $this->actionName,
                'readOnly' => true,
                'class' => 'form-control input',
            ],
        ]);

        $roleSelect = new Element\Select('allowedRoles');
        $roleSelect->setOptions([
            'label' => 'Select Allowed Roles for action',
            'empty_option' => '--Select Allowed Role--',
        ]);
        $roleSelect->setValueOptions($this->roleNames);
        $roleSelect->setAttributes([
            'multiple' => true,
            'class' => 'form-control input',
        ]);

        $this->add($roleSelect);
    }

    public function getInputFilterSpecification(): array {
        return [
            'allowedRoles' => [
                'required' => false
            ],
        ];
    }

}
