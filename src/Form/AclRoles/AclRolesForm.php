<?php

namespace BplAdmin\Form\AclRoles;

use Laminas\Form\Element;

class AclRolesForm extends \Laminas\Form\Form {

    public function __construct(\Doctrine\Persistence\ObjectManager $persistanceManager, $name = NULL, $options = []) {
        parent::__construct($name, $options);

        $aclRolesFieldset = new \BplAdmin\Form\AclRoles\AclRolesFieldset($persistanceManager, $name, $options);
        $aclRolesFieldset->setUseAsBaseFieldset(true);
        $this->add($aclRolesFieldset);
        $this->setAttribute("enctype", "multipart/form-data");
        $this->setAttribute("METHOD", "POST");

        $csrf = new Element\Csrf('csrf');
        $csrf->getCsrfValidator()->setTimeout(600);
        $this->add($csrf);
        
        $submitElement = new Element\Button('submit');
        $submitElement
                ->setLabel('Register')
                ->setAttributes([
                    'type' => 'submit',
                    'class' => 'btn btn-success'
        ]);

        $this->add($submitElement, [
            'priority' => -100,
        ]);
    }

}
