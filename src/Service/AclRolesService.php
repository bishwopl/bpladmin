<?php 
namespace BplAdmin\Service;

use BplAdmin\Form\AclRoles\AclRolesForm;
use CirclicalUser\Entity\Role;

class AclRolesService extends \BplCrud\Crud
{
    public function __construct(\Doctrine\ORM\EntityManagerInterface $persistanceManager)
    {
        $form = new AclRolesForm($persistanceManager, "aclRoles");
        $mapper = new \BplCrud\Mapper\DoctrineMapper($persistanceManager, Role::class);
        parent::__construct($mapper, $form, Role::class);
    }
}
