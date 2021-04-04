<?php

namespace BplAdmin\Controller;

use BplAdmin\ModuleOpions\CrudOptions;
use BplCrud\Contract\CrudInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class RoleManagementController extends AbstractActionController {

    /**
     * @var \BplCrud\Contract\CrudInterface 
     */
    private $roleService;

    /**
     * @var \BplAdmin\ModuleOpions\CrudOptions
     */
    private $options;

    public function __construct(CrudOptions $options, CrudInterface $roleService) {
        $this->roleService = $roleService;
        $this->options = $options;
    }

    public function indexAction(): ViewModel {
        $pageNo = $this->params()->fromRoute('id1');
        $pageNo = $pageNo !== NULL ? $pageNo : 1;
        $startIndex = ($pageNo - 1) * $this->options->getItemsPerPage();

        $roles = $this->roleService->read([], $startIndex, $this->options->getItemsPerPage(), ['id' => 'DESC']);

        return new ViewModel([
            "roles" => $roles,
            "totalRecordCount" => $this->roleService->getTotalRecordCount([]),
            "noOfPages" => $this->roleService->getNoOfPages([], $this->options->getItemsPerPage()),
            "startIndex" => $startIndex,
            "currentPage" => $pageNo
        ]);
    }

    public function addAction(): ViewModel {
        $created = false;
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $this->roleService->setData($data);

            if ($this->roleService->isFormValid()) {
                $this->roleService->create($this->roleService->getObject());
                $created = true;
            }
        }
        return new ViewModel([
            'form' => $this->roleService->getForm(),
            'created' => $created
        ]);
    }

    public function editAction(): ViewModel {
        $edited = false;
        $id = $this->params()->fromRoute('id1');

        $rolePaginator = $this->roleService->read(['id' => $id]);
        if ($rolePaginator->count() !== 1) {
            throw new \Exception("Role not found");
        }
        $rolePaginator->getIterator()->seek(0);
        $role = $rolePaginator->getIterator()->current();
        $this->roleService->bind($role);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $this->roleService->setData($data);

            if ($this->roleService->isFormValid()) {
                $this->roleService->update($this->roleService->getObject());
                $edited = true;
            }
        }
        return new ViewModel([
            'form' => $this->roleService->getForm(),
            'edited' => $edited
        ]);
    }

    public function deleteAction(): ViewModel {
        $id = $this->params()->fromRoute('id1');
        $rolePaginator = $this->roleService->read(['id' => $id]);

        if ($rolePaginator->count() !== 1) {
            throw new \Exception("Role not found");
        }
        $rolePaginator->getIterator()->seek(0);
        $role = $rolePaginator->getIterator()->current();
        $this->roleService->bind($role);

        if ($this->getRequest()->isPost()) {
            $this->roleService->delete($role);
            $this->redirect()->toRoute('bpl-admin/role-management');
        }
        return new ViewModel([
            'form' => $this->roleService->getForm(),
            'role' => $role
        ]);
    }

}
