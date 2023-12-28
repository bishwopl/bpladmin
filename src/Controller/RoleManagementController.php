<?php

namespace BplAdmin\Controller;

use BplAdmin\ModuleOpions\CrudOptions;
use CirclicalUser\Provider\RoleProviderInterface;
use CirclicalUser\Entity\Role;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Form\Form;

class RoleManagementController extends AbstractActionController {

    /**
     * @var \CirclicalUser\Provider\RoleProviderInterface 
     */
    private $roleMapper;

    /**
     * @var \BplAdmin\ModuleOpions\CrudOptions
     */
    private $options;
    
    /**
     * @var \Laminas\Form\Form
     */
    private $roleForm;

    public function __construct(
            CrudOptions $options,
            RoleProviderInterface $roleMapper,
            Form $roleForm
    ) {
        $this->options = $options;
        $this->roleMapper = $roleMapper;
        $this->roleForm = $roleForm;
    }

    public function indexAction(): ViewModel {
        $pageNo = $this->params()->fromRoute('id') !== NULL ? $this->params()->fromRoute('id') : 1;
        $startIndex = ($pageNo - 1) * $this->options->getItemsPerPage();

        $roles = [];
        $allRoles = $this->roleMapper->getAllRoles();
        for ($i = $startIndex; $i < $startIndex + $this->options->getItemsPerPage(); $i++) {
            if (!isset($allRoles[$i])) {
                break;
            }
            $roles[] = $allRoles[$i];
        }
        
        return new ViewModel([
            "roles" => $roles,
            "totalRecordCount" => sizeof($allRoles),
            "noOfPages" => (int) ceil(sizeof($allRoles) / $this->options->getItemsPerPage()),
            "startIndex" => $startIndex,
            "currentPage" => $pageNo
        ]);
    }

    public function addAction(): ViewModel {
        $created = false;
        $data = $this->getRequest()->getPost();
        $this->roleForm->setData($data);

        if ($this->getRequest()->isPost() && $this->roleForm->isValid()) {
            $roleName = $this->roleForm->get('name')->getValue();
            $parentName = $this->roleForm->get('parent')->getValue();

            if ($this->roleMapper->getRoleWithName($roleName) instanceof RoleProviderInterface) {
                throw new \Exception("Role with name $roleName already exists !");
            }

            $parent = trim($parentName) !== '' ? $this->roleMapper->getRoleWithName($parentName) : NULL;
            $role = new Role($roleName, $parent);
            
            $this->roleMapper->save($role);
            $created = true;
        }

        return new ViewModel([
            'form' => $this->roleForm,
            'created' => $created
        ]);
    }

    public function editAction(): ViewModel {
        $edited = false;
        $id = $this->params()->fromRoute('id');
        $role = NULL;
        $allRoles = $this->roleMapper->getAllRoles();
        foreach ($allRoles as $r) {
            if ($r->getId() == $id) {
                $role = $r;
                break;
            }
        }

        if (!$role instanceof \CirclicalUser\Provider\RoleInterface) {
            throw new \Exception("Role not found!");
        }

        if (!$this->getRequest()->isPost()) {
            $this->roleForm->setData([
                "name" => $role->getname(),
                "parent" => $role->getParent() !== NULL ? $role->getParent()->getName() : NULL
            ]);
        }

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $this->roleForm->setData($data);
            if ($this->roleForm->isValid()) {
                $roleName = $this->roleForm->get('name')->getValue();
                $parentName = $this->roleForm->get('parent')->getValue();
                $parent = trim($parentName) !== '' ? $this->roleMapper->getRoleWithName($parentName) : NULL;
                
                $newRole = new Role($roleName, $parent);
                $newRole->setId($role->getId());
                $this->roleMapper->update($newRole);
   
                $edited = true;
            }
        }
        return new ViewModel([
            'form' => $this->roleForm,
            'edited' => $edited
        ]);
    }

    public function deleteAction(): ViewModel {
        $id = $this->params()->fromRoute('id');
        $role = NULL;
        $allRoles = $this->roleMapper->getAllRoles();
        foreach ($allRoles as $r) {
            if ($r->getId() == $id) {
                $role = $r;
                break;
            }
        }
        
        if (!$role instanceof \CirclicalUser\Provider\RoleInterface) {
            throw new \Exception("Role not found!");
        }

        if ($this->getRequest()->isPost()) {
            $this->roleMapper->delete($role);
            $this->redirect()->toRoute('bpl-admin/role-management');
        }
        return new ViewModel([
            'role' => $role
        ]);
    }

}
