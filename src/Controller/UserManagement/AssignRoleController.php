<?php

namespace BplAdmin\Controller\UserManagement;

use CirclicalUser\Provider\UserProviderInterface;
use CirclicalUser\Provider\RoleProviderInterface;
use BplUser\Provider\BplUserInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Form\Form;

class AssignRoleController extends AbstractActionController {

    /**
     * @var \Laminas\Form\Form
     */
    private $assignRoleForm;

    /**
     * @var \CirclicalUser\Provider\UserProviderInterface 
     */
    private $userMapper;

    /**
     * @var \CirclicalUser\Provider\RoleProviderInterface 
     */
    private $roleMapper;

    public function __construct(
            Form $assignRoleForm,
            UserProviderInterface $userMapper,
            RoleProviderInterface $roleMapper
    ) {
        $this->assignRoleForm = $assignRoleForm;
        $this->userMapper = $userMapper;
        $this->roleMapper = $roleMapper;
    }

    public function indexAction(): ViewModel {
        $created = false;

        $userId = $this->params()->fromRoute('user_id');

        $user = $this->userMapper->getUser($userId);
        if (!$user instanceof BplUserInterface) {
            throw new \Exception('User not found');
        }

        $this->assignRoleForm->populateValues(["roles" => $user->getRoles()]);
        $data = $this->getRequest()->getPost();
        $this->assignRoleForm->setData($data);

        if ($this->getRequest()->isPost() && $this->assignRoleForm->isValid()) {
            $allRoles = $this->roleMapper->getAllRoles();
            $selectedRolesIds = $this->assignRoleForm->get('roles')->getValue();
            
            foreach ($allRoles as $roleInStorage) {
                if (in_array($roleInStorage->getId(), $selectedRolesIds)) {
                    if(!$user->hasRole($roleInStorage)){
                        $user->addRole($roleInStorage);
                    }
                    continue;
                }
                $user->removeRole($roleInStorage);    
            }
            $this->userMapper->update($user);
            $created = true;
        }
        return new ViewModel([
            'user' => $user,
            'form' => $this->assignRoleForm,
            'created' => $created
        ]);
    }

}
