<?php

namespace BplAdmin\Service;

use BplAdmin\Entity\AppPermission;
use BplAdmin\ModuleOpions\AccessOptions;
use CirclicalUser\Provider\RoleProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Laminas\Config\Writer\PhpArray as ConfigWriter;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\ArrayProvider;

class ControllerGuardConfigManager {

    protected AppPermission $appPermissions;

    protected AccessOptions $aclOptions;

    private RoleProviderInterface $roleMapper;
    
    protected Collection $controllerPermissions;

    protected array $applicationConfigArray;

    protected bool $initialized = false;

    public function __construct(AccessOptions $aclOptions, RoleProviderInterface $roleMapper, array $applicationConfigArray) {
        $this->aclOptions = $aclOptions;
        $this->roleMapper = $roleMapper;
        $this->applicationConfigArray = $applicationConfigArray;
        $this->controllerPermissions = new ArrayCollection();
        $this->initializeConfig();
    }

    public function initializeConfig(): void {
        $this->appPermissions = AppPermission::cretaeFromConfig($this->applicationConfigArray['circlical']['user']['guards']);
        $this->initialized = true;
    }

    /**
     * @return AppPermission
     */
    public function getAppGuardConfig(): AppPermission {
        return $this->appPermissions;
    }

    public function writeConfig(AppPermission $a = NULL): void {
        if( $a== NULL){
            $a = $this->appPermissions;
        }
        $configFileName = $this->aclOptions->getAclFileLocation() . '/' . $this->aclOptions->getAclFilename();
        if (!$this->initialized) {
            //$this->initializeConfig();
        }
        if (is_file($configFileName)) {
            unlink($configFileName);
        }
        
        
        $configAggregator = new ConfigAggregator([new ArrayProvider([]), new ArrayProvider($a->toConfigArray())]);

        $writer = new ConfigWriter();
        $writer->setUseBracketArraySyntax(true);
        $writer->setUseClassNameScalars(true);
        $writer->toFile($configFileName, $configAggregator->getMergedConfig());
    }

    public function getControllerNames(): array {
        $defaultControllers = array_keys($this->applicationConfigArray['controllers']['factories']);
        $restControllers = array_keys($this->applicationConfigArray['api-tools-rest']);
        $allControllerNames = array_merge($defaultControllers, $restControllers);
        sort($allControllerNames);
        foreach ($allControllerNames as $key=>$value){
            if(strpos($value, 'Cli')){
                unset($allControllerNames[$key]);
            }
            if(strpos($value, 'Console')){
                unset($allControllerNames[$key]);
            }
        }
        return array_values($allControllerNames);
    }

    public function getActionNames($controller): array {
        $actions = [];
        $className = is_object($controller) ? get_class($controller) : $controller;
        if(!class_exists($className)){
            $className .= 'Controller';
            if(!class_exists($className)){
                return [];
            }
        }
        $methods = get_class_methods($className);
        $methods = $methods == NULL ? [] : $methods;
        foreach ($methods as $m) {
            if (strpos($m, 'Action') !== false && $m !== 'notFoundAction' && $m !== 'getMethodFromAction') {
                $actions[] = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', str_replace('Action', '', $m)));
            }
        }
        return $actions;
    }

    public function getConfigForForm() {
        $controllerNames = $this->getControllerNames();
        $ret = [];
        foreach ($controllerNames as $c) {
            $ret[$c . '.' . 'default'] = $this->appPermissions->getDefaultControllerRoles($c);
            $actionNames = $this->getActionNames($c);
            foreach ($actionNames as $a) {
                $a = str_replace('Action', '', $a);
                $ret[$c . '.' . $a] = $this->appPermissions->getAllowedRoles($c, $a);
            }
        }
        return $ret;
    }

}
