<?php 
namespace BplAdmin\Service;


class UserService extends \BplCrud\Crud
{
    public function __construct(
            \Doctrine\ORM\EntityManagerInterface $persistanceManager, 
            \Laminas\Form\FormInterface $userProfileForm,
            $userEntityName)
    {
        $mapper = new \BplCrud\Mapper\DoctrineMapper($persistanceManager, $userEntityName);
        parent::__construct($mapper, $userProfileForm, $userEntityName);
    }
}
