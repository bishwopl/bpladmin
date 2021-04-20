<?php

namespace BplAdmin\Controller\AccessManagement;

use BplAdmin\Service\ControllerGuardConfigManager;
use BplAdmin\Form\AppPermissionForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AccessListController extends AbstractActionController {

    /**
     * @var \BplAdmin\Service\ControllerGuardConfigManager 
     */
    protected $controllerAclService;
    
    /**
     * @var \BplAdmin\Form\AppPermissionForm
     */
    protected $appPermissionForm;

    /**
     * @var array 
     */
    protected $roleNames;

    /**
     * @param ControllerGuardConfigManager $controllerAclService
     * @param array $roleNames
     */
    public function __construct(ControllerGuardConfigManager $controllerAclService, AppPermissionForm $appPermissionForm, array $roleNames) {
        $this->controllerAclService = $controllerAclService;
        $this->appPermissionForm = $appPermissionForm;
        $this->roleNames = $roleNames;
    }

    public function indexAction(): ViewModel {
        $this->controllerAclService->initializeConfig();
        $vm = new ViewModel();
        $vm->setTerminal(true);
        $vm->setVariables([
            'appConfig' => $this->controllerAclService->getAppGuardConfig(),
            'controllerNames' => $this->controllerAclService->getControllerNames(),
            'form' => $this->appPermissionForm,
            'configArray' => $this->controllerAclService->getConfigForForm(),
            'roleNames' => $this->roleNames,
        ]);
        
        var_dump($this->getRequest()->getPost()->toArray());
        
        return $vm;
    }

}
