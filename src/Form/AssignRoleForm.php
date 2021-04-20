<?php

namespace BplAdmin\Form;

use Laminas\Form\Element;

class AssignRoleForm extends \Laminas\Form\Form {

    public function __construct($roleNames, $name = NULL, $options = []) {
        parent::__construct($name, $options);

        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('METHOD', 'POST');

        $parentSelect = new Element\Select('roles');
        $parentSelect->setOptions([
            'label' => 'Assign Roles',
            'empty_option' => '--Assign role--',
        ]);
        $parentSelect->setValueOptions($roleNames);
        $parentSelect->setAttributes([
            'id' => 'parentId',
            'placeholder' => 'parent',
            'multiple' => true,
            'class' => 'form-control input',
            'style' => 'min-height: 200px;'
        ]);

        $this->add($parentSelect);
        
        $csrf = new Element\Csrf('csrf');
        $csrf->getCsrfValidator()->setTimeout(600);
        $this->add($csrf);
        
        $submitElement = new Element\Button('submit');
        $submitElement
                ->setLabel('Assign Roles')
                ->setAttributes([
                    'type' => 'submit',
                    'class' => 'btn btn-success'
        ]);

        $this->add($submitElement, [
            'priority' => -100,
        ]);
    }

}
