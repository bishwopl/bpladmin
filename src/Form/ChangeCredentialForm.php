<?php

namespace BplAdmin\Form;

use Laminas\Form\Element;

class ChangeCredentialForm extends \Laminas\Form\Form implements \Laminas\InputFilter\InputFilterProviderInterface {

    public function __construct($name = NULL, $options = []) {
        parent::__construct($name, $options);

        $this->setAttribute("enctype", "multipart/form-data");
        $this->setAttribute("METHOD", "POST");

        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                'type' => 'password',
                'required' => 'true',
                'class' => 'form-control input',
            ],
        ]);

        $this->add([
            'name' => 'password_verify',
            'type' => 'password',
            'options' => [
                'label' => 'Verify Password',
            ],
            'attributes' => [
                'type' => 'password',
                'required' => 'true',
                'class' => 'form-control input'
            ],
        ]);
        
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
            "password" => [
                "required" => true,
                "validators" => [
                    [
                        "name" => \Laminas\Validator\StringLength::class,
                        "options" => [
                            "min" => 8,
                            "max" => 256,
                        ]
                    ]
                ],
            ],
            "password_verify" => [
                "required" => true,
                "validators" => [
                    [
                        "name" => \Laminas\Validator\Identical::class,
                        "options" => [
                            "token" => 'password'
                        ]
                    ]
                ],
            ],
        ];
    }

}
