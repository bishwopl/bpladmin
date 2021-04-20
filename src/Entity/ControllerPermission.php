<?php

namespace BplAdmin\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class ControllerPermission {

    /**
     * @var string
     */
    protected $controllerName;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $defaultAllowedRoles;

    /**
     * @var bool
     */
    protected $allowAllByDefault = false;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $actionPermissions;

    private function __construct() {
        $this->actionPermissions = new ArrayCollection();
        $this->defaultAllowedRoles = new ArrayCollection();
    }

    public function setControllerName(string $controllerName): ControllerPermission {
        $this->controllerName = $controllerName;
        return $this;
    }

    public function addDefaultAllowedRole(string $role) {
        $this->defaultAllowedRoles->set($role, $role);
        return;
    }

    public function removeDefaultAllowedRole(string $role) {
        $this->defaultAllowedRoles->removeElement($role);
        return;
    }

    public function getControllerName(): string {
        return $this->controllerName;
    }

    public function getActionPermissions(): ArrayCollection {
        return $this->actionPermissions;
    }

    public function addActionPermission(ActionPermission $a) {
        $this->actionPermissions->set($a->getActionName(), $a);
        return;
    }

    public function removeActionPermission(ActionPermission $a) {
        $this->actionPermissions->removeElement($a);
        return;
    }

    public function getAllowAllByDefault(): bool {
        return $this->allowAllByDefault;
    }

    public function getDefaultAllowedRoles(): ArrayCollection{
        return $this->defaultAllowedRoles;
    }
    
    public function setAllowAllByDefault(bool $allowAllByDefault): ControllerPermission {
        $this->allowAllByDefault = $allowAllByDefault;
        return $this;
    }

    public function removeAllDefaultRoles(){
        $this->defaultAllowedRoles = new ArrayCollection([]);
    }
    
    /**
     * Takes an array with following format an creates ControllerPermission object
     * @param string $controllerName
     * @param array $configArray
     */
    public static function createFromArray($controllerName, $configArray): ControllerPermission {
        $obj = new ControllerPermission();
        $obj->controllerName = $controllerName;
        $countDefaultRoles = 0;
        if (isset($configArray['default']) && is_array($configArray['default'])) {
            foreach ($configArray['default'] as $roleName) {
                $obj->addDefaultAllowedRole($roleName);
                $countDefaultRoles++;
            }
            $obj->setAllowAllByDefault($countDefaultRoles == 0);
        }
        if (isset($configArray['actions'])) {
            foreach ($configArray['actions'] as $actionName => $allowedRoles) {
                $actionPermission = ActionPermission::createFromArray($actionName, $allowedRoles);
                $obj->addActionPermission($actionPermission);
            }
        }
        return $obj;
    }

    public function toArray(): array {
        $actionPermissionConfig = [];
        foreach ($this->actionPermissions as $a) {
            $actionPermissionConfig = array_merge($actionPermissionConfig, $a->toArray());
        }
        $ret = [
            $this->controllerName => [
                'default' => $this->defaultAllowedRoles->toArray(),
                'actions' => $actionPermissionConfig,
            ]
        ];

        //remove 'default' config
        if ($this->allowAllByDefault !== true && $this->defaultAllowedRoles->count() == 0) {
            unset($ret[$this->controllerName]['default']);
        }

        //remove 'action' config
        if ($this->actionPermissions->count() == 0) {
            unset($ret[$this->controllerName]['actions']);
        }

        return $ret;
    }

    public function allowRolesToAccessAction(string $actionName, array $roles): void {
        $actionGuard = $this->getActionGaurd($actionName);
        if ($actionGuard == NULL) {
            $actionGuard = ActionPermission::createFromArray($actionName, $roles);
        } else {
            $key = $this->actionPermissions->indexOf($actionGuard);
            $this->actionPermissions->remove($key);
            foreach($roles as $role){
                $actionGuard->addAllowedRole($role);
            }
        }
        $this->addActionPermission($actionGuard);
        return;
    }
    
    public function removeRolesToAccessAction(string $actionName, array $roles): void {
        $actionGuard = $this->getActionGaurd($actionName);
        if ($actionGuard instanceof ActionPermission) {
            $key = $this->actionPermissions->indexOf($actionGuard);
            $this->actionPermissions->remove($key);
            foreach($roles as $role){
                $actionGuard->removeAllowedRole($role);
            }
            $this->addActionPermission($actionGuard);
        }
        return;
    }

    private function getActionGaurd(string $actionName): ?ActionPermission {
        foreach ($this->actionPermissions as $a) {
            if ($a->getActionName() == $actionName) {
                return $a;
            }
        }
        return NULL;
    }

}
