<?php

namespace BplAdmin\Controller\AccessManagement;

use BplAdmin\Service\ControllerGuardConfigManager;
use BplAdmin\Service\ResourceGuardConfigManager;
use BplAdmin\Form\AppPermissionForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Ramsey\Uuid\Uuid;

class AccessListController extends AbstractActionController {

    public function __construct(
            private ControllerGuardConfigManager $controllerAclService,
            private ResourceGuardConfigManager $resourceGuardConfigManager,
            private AppPermissionForm $appPermissionForm,
            private array $roleNames
    ) {}

    public function indexAction(): ViewModel {
        $vm = new ViewModel();
        return $vm;
    }

    public function controllerAction(): ViewModel {
        $this->controllerAclService->initializeConfig();

        $controllerNames = $this->controllerAclService->getControllerNames();
        $actionNames = [];

        foreach ($controllerNames as $c) {
            $actionNames[$c] = $this->controllerAclService->getActionNames($c);
        }

        $vm = new ViewModel([
            'availableControllers' => $actionNames,
            'originalConfig' => $this->controllerAclService->getAppGuardConfig()->toControllerWiseArray(),
            'roleNames' => $this->roleNames
        ]);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();
            foreach ($data as $controllerName => $config) {
                if (sizeof($config['default']) == 0 || in_array('none', $config['default'])) {
                    $data[$controllerName]['default'] = [Uuid::uuid4()->toString() . " Random role name so that CirclicalUser doesnot throes exception"];
                } elseif (in_array('all', $config['default'])) {
                    $data[$controllerName]['default'] = [];
                }
            }
            $appPermission = \BplAdmin\Entity\AppPermission::cretaeFromConfig([['controllers' => $data]]);
            $this->controllerAclService->writeConfig($appPermission);
            $this->redirect()->toRoute('bpl-admin/access-management', ['action' => 'controller']);
        }

        return $vm;
    }

    public function resourceAction(): ViewModel {
        $vm = new ViewModel();
        $vm->setVariable('listOfResources', $this->resourceGuardConfigManager->getTabularList());
        return $vm;
    }

    public function resourceAclListAction() {
        $vm = new ViewModel();
        $resourceName = $this->params()->fromRoute('identifier');
        if (is_null($resourceName)) {
            return $this->redirect()->toRoute('bpl-admin/access-management', ['action' => 'resource']);
        }
        $resourceName = urldecode($resourceName);
        $vm->setVariable('listOfAcls', $this->resourceGuardConfigManager->getAcls($resourceName));
        $vm->setVariable('roleNames', $this->resourceGuardConfigManager->getAllRole());
        $vm->setVariable('resourceName', $resourceName);
        return $vm;
    }

    public function resourceAclAddAction() {
        $vm = new ViewModel();
        $resourceName = $this->params()->fromRoute('identifier');
        if (is_null($resourceName)) {
            return $this->redirect()->toRoute('bpl-admin/access-management', ['action' => 'resource']);
        }
        $resourceName = urldecode($resourceName);
        $vm->setVariable('listOfAcls', $this->resourceGuardConfigManager->getAcls($resourceName));
        $vm->setVariable('listOfActions', $this->resourceGuardConfigManager->resourceActionList[$resourceName]);
        $vm->setVariable('roleNames', $this->resourceGuardConfigManager->getAllRole());
        $vm->setVariable('resourceName', $resourceName);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();
            if (trim($data['role']) == '' || sizeof($data['actions']) == 0) {
                $vm->setVariable('message', "Please select a role and actions.");
                return $vm;
            }
            $response = $this->resourceGuardConfigManager->createAcl($resourceName, $data['role'], $data['actions']);
            if ($response === true) {
                $this->redirect()->toRoute('bpl-admin/access-management', [
                    'action' => 'resource-acl-list',
                    'identifier' => urlencode($resourceName)
                ]);
            } else {
                $vm->setVariable('message', $response);
            }
        }

        return $vm;
    }

    public function resourceAclDeleteAction() {
        $resourceName = $this->params()->fromRoute('identifier');
        $roleName = $this->params()->fromRoute('roleName');

        if ($this->getRequest()->isPost()) {
            $this->resourceGuardConfigManager->deleteAcl(urldecode($resourceName), urldecode($roleName));
        }
        return $this->redirect()->toRoute('bpl-admin/access-management', [
                    'action' => 'resource-acl-list',
                    'identifier' => $resourceName
        ]);
    }
}
