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
        $this->controllerAclService->initializeConfig();
        $this->appPermissionForm->setData($this->controllerAclService->getAppGuardConfig()->getArrayCopy());
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            $this->appPermissionForm->setData($data);
            if($this->appPermissionForm->isValid()){
                $h = new ArraySerializableHydrator();
                $obj = \BplAdmin\Entity\AppPermission::getEmptyObject();
                $h->hydrate($this->appPermissionForm->getData(), $obj);
                $this->controllerAclService->writeConfig($obj);
            }
        }
        
        $vm = new ViewModel();
        $vm->setVariables([
            'appConfig' => $this->controllerAclService->getAppGuardConfig(),
            'controllerNames' => $this->controllerAclService->getControllerNames(),
            'form' => $this->appPermissionForm,
            'configArray' => $this->controllerAclService->getAppGuardConfig()->getArrayCopy(),
            'roleNames' => $this->roleNames,
        ]);
        return $vm;
    }

}
