<?php

namespace BplAdmin\Form\Fieldset;

use Laminas\Form\Fieldset;
use Laminas\Form\Element;
use Laminas\InputFilter\InputFilterProviderInterface;

class ActionPermissionFieldset extends Fieldset implements InputFilterProviderInterface {
    
    /**
     * @var array 
     */
    protected $roleNames;

    public function __construct(array $roleNames) {
        $this->roleNames = $roleNames;
        parent::__construct('action-fieldset', []);
    }
    
    public function init() {
        $this->add([
            'type' => Element\Text::class,
            'name' => 'actionName',
            'options' => [
                'label' => 'Action Name',
            ],
        ]);

        $roleSelect = new Element\Select('allowedRoles');
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
    }

    public function getInputFilterSpecification(): array {
        return [];
    }

}
