<?php

namespace BplAdmin\Form\AclRoles;

class AclRolesFieldset extends \Laminas\Form\Fieldset implements \Laminas\InputFilter\InputFilterProviderInterface {

    public function __construct(\Doctrine\Persistence\ObjectManager $persistanceManager, $name = NULL, $options = []) {
        parent::__construct($name, $options);

        $this->setHydrator(new \Doctrine\Laminas\Hydrator\DoctrineObject($persistanceManager, false));
        $this->setObject(new \CirclicalUser\Entity\Role());

        $this->add([
            "type" => \Laminas\Form\Element\Hidden::class,
            "name" => "id",
            "options" => [
                "label" => "id",
            ],
            "attributes" => [
                "id" => "idId",
                "placeholder" => "id",
                "class" => "",
            ],
        ]);
        $this->add([
            "type" => \Laminas\Form\Element\Text::class,
            "name" => "name",
            "options" => [
                "label" => "Role Name",
            ],
            "attributes" => [
                "id" => "nameId",
                "placeholder" => "Name",
                'class' => 'form-control input'
            ],
        ]);
        $this->add([
            "name" => "parent",
            "type" => \DoctrineModule\Form\Element\ObjectSelect::class,
            "options" => [
                "label" => "Parent Role",
                "object_manager" => $persistanceManager,
                "target_class" => \CirclicalUser\Entity\Role::class,
                "property" => "name",
                "display_empty_item" => true,
                "empty_item_label" => "--Select parent role--",
            ],
            "attributes" => [
                "id" => "parentId",
                "placeholder" => "parent",
                'class' => 'form-control input'
            ],
        ]);
    }

    public function getInputFilterSpecification() {
        return [
            "id" => [
                "required" => false,
                "filters" => [],
                "validators" => [],
            ],
            "name" => [
                "required" => true,
                "filters" => [],
                "validators" => [
                    [
                        "name" => \Laminas\I18n\Validator\Alpha::class,
                        "options" => [
                            "allowWhiteSpace" => true
                        ]
                    ]
                ],
            ],
            "parent" => [
                "required" => false,
                "filters" => [],
                "validators" => [],
            ],
        ];
    }

}
