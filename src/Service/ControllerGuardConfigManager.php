<?php

namespace BplAdmin\Service;

use BplAdmin\Entity\AppPermission;
use BplAdmin\ModuleOpions\AccessOptions;
use CirclicalUser\Provider\RoleProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\Config\Writer\PhpArray as ConfigWriter;

class ControllerGuardConfigManager {

    /**
     * @var \BplAdmin\Entity\AppPermission
     */
    protected $appPermissions;

    /**
     * @var \BplAdmin\ModuleOpions\AccessOptions 
     */
    protected $aclOptions;

    /**
     * @var \CirclicalUser\Provider\RoleProviderInterface 
     */
    private $roleMapper;

    /**
     * @var array
     */
    protected $applicationConfigArray;

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @param \BplAdmin\ModuleOpions\AccessOptions $aclOptions
     * @param array $applicationConfigArray
     */
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

    public function getAppGuardConfig(): AppPermission {
        return $this->appPermissions;
    }

    public function writeConfig(): void {
        $configFileName = $this->aclOptions->getAclFileLocation() . '/' . $this->aclOptions->getAclFilename();
        if (!$this->initialized) {
            $this->initializeConfig();
        }
        if (is_file($configFileName)) {
            unlink($configFileName);
        }
        $writer = new ConfigWriter();
        $writer->setUseBracketArraySyntax(true);
        $writer->setUseClassNameScalars(true);
        $writer->toFile($configFileName, $this->appPermissions->toArray());
    }

    public function getControllerNames(): array {
        return array_keys($this->applicationConfigArray['controllers']['factories']);
    }

    public function getActionNames($controller): array {
        $actions = [];
        $className = is_object($controller) ? get_class($controller) : $controller;
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
