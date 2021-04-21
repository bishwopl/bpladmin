<?php

namespace BplAdmin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Laminas\Stdlib\ArraySerializableInterface;

class ActionPermission implements ArraySerializableInterface {

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

    public static function createFromConfigArray(string $actionName, array $allowedRoles): ActionPermission {
        $obj = new ActionPermission();
        $obj->actionName = $actionName;
        foreach ($allowedRoles as $roleName) {
            $obj->addAllowedRole($roleName);
        }
        return $obj;
    }

    public function toConfigArray(): array {
        return $this->allowedRoles->count()>0?[
            $this->actionName => $this->allowedRoles->toArray()
        ]:[];
    }

    /**
     * @param array $array
     * @return void
     */
    public function exchangeArray(array $array): void {
        $this->actionName = $array['actionName'];
        if(isset($array['allowedRoles']) && is_array($array['allowedRoles'])){
            foreach($array['allowedRoles'] as $role){
                $this->addAllowedRole($role);
            }
        }
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array {
        return [
            'actionName' => $this->actionName,
            'allowedRoles' => $this->allowedRoles->toArray()
        ];
    }
    
    public static function getEmptyObject(): ActionPermission {
        return new ActionPermission();
    }

}
