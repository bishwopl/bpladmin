<?php

namespace BplAdmin\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class ActionPermission {

    /**
     * @var string
     */
    protected $actionName;

    /**
     * List of roles that are allowed to access the action
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $allowedRoles;

    private function __construct() {
        $this->allowedRoles = new ArrayCollection();
    }

    public function getActionName(): string {
        return $this->actionName;
    }

    public function setActionName(string $actionName) {
        $this->actionName = $actionName;
        return $this;
    }

    public function addAllowedRole(string $role) {
        $this->allowedRoles->set($role, $role);
        return;
    }

    public function removeAllowedRole(string $role) {
        $this->allowedRoles->removeElement($role);
        return;
    }
    
    public function removeAllRoles(){
        $this->allowedRoles = new ArrayCollection([]);
        return;
    }
    
    public function getAllowedRoles() : ArrayCollection{
        return $this->allowedRoles;
    }

    public static function createFromArray(string $actionName, array $allowedRoles): ActionPermission {
        $obj = new ActionPermission();
        $obj->actionName = $actionName;
        foreach ($allowedRoles as $roleName) {
            $obj->addAllowedRole($roleName);
        }
        return $obj;
    }

    public function toArray(): array {
        return [
            $this->actionName => $this->allowedRoles->toArray()
        ];
    }

}
