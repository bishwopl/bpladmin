<?php

namespace BplAdmin\Form;

use Laminas\Form\Element;

class AssignRoleForm extends \Laminas\Form\Form {

    public function __construct(\Doctrine\Persistence\ObjectManager $persistanceManager, $name = NULL, $options = []) {
        parent::__construct($name, $options);

        $this->setAttribute("enctype", "multipart/form-data");
        $this->setAttribute("METHOD", "POST");

        $this->add([
            "name" => "roles",
            "type" => \DoctrineModule\Form\Element\ObjectSelect::class,
            "options" => [
                "label" => "Parent Role",
                "object_manager" => $persistanceManager,
                "target_class" => \CirclicalUser\Entity\Role::class,
                "property" => "name",
                "display_empty_item" => false,
                "empty_item_label" => "--Select User Roles--",
            ],
            "attributes" => [
                "multiple" => true,
                "id" => "rolesId",
                'class' => 'form-control input'
            ],
        ]);
        
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
