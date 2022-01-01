<?php

namespace BplAdmin\Controller\AccessManagement;

use BplAdmin\Service\ControllerGuardConfigManager;
use BplAdmin\Form\AppPermissionForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Hydrator\ArraySerializableHydrator;

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
        $roleId = $this->params()->fromRoute('role_id');
        
        $this->controllerAclService->initializeConfig();
        \Symfony\Component\VarDumper\VarDumper::dump($this->controllerAclService->getAppGuardConfig()->toConfigArray());
        die();
        
        $vm = new ViewModel();
        return $vm;
    }

}
