<?php

namespace BplAdmin\Form;

use Laminas\Form\Element;

class RoleForm extends \Laminas\Form\Form implements \Laminas\InputFilter\InputFilterProviderInterface {

    public function __construct($roleNames, $name = NULL, $options = []) {
        parent::__construct($name, $options);

        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('METHOD', 'POST');

        $this->add([
            'type' => \Laminas\Form\Element\Hidden::class,
            'name' => 'id',
            'options' => [
                'label' => '',
            ],
        ]);

        $nameElement = new Element\Text('name');
        $nameElement->setOptions(['label' => 'Role Name',]);
        $nameElement->setAttributes([
            'id' => 'nameId',
            'placeholder' => 'Name',
            'require' => true,
            'class' => 'form-control input'
        ]);
        $this->add($nameElement);


        $parentSelect = new Element\Select('parent');
        $parentSelect->setOptions([
            'label' => 'Parent Role',
            'empty_option' => '--Select parent role--',
        ]);
        $parentSelect->setValueOptions($roleNames);
        $parentSelect->setAttributes([
            'id' => 'parentId',
            'placeholder' => 'parent',
            'class' => 'form-control input'
        ]);

        $this->add($parentSelect);

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

        $this->add($submitElement, [
            'priority' => -100,
        ]);
    }

    public function getInputFilterSpecification(): array {
        return [
            'name' => [
                'required' => true,
                'filters' => [],
                'validators' => [
                    [
                        'name' => \Laminas\I18n\Validator\Alpha::class,
                        'options' => [
                            'allowWhiteSpace' => true
                        ]
                    ]
                ],
            ],
            'parent' => [
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
        ];
    }

}
