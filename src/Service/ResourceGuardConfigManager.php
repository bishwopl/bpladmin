<?php

namespace BplAdmin\Service;

use CirclicalUser\Service\AccessService;
use CirclicalUser\Provider\RoleProviderInterface;
use CirclicalUser\Provider\GroupPermissionProviderInterface;
use CirclicalUser\Provider\RoleInterface;
use CirclicalUser\Provider\GroupPermissionInterface;

class ResourceGuardConfigManager {

    public function __construct(
            private AccessService $accessService,
            private RoleProviderInterface $roleMapper,
            private GroupPermissionProviderInterface $groupPermissionMapper,
            public array $resourceActionList
    ) { }

    public function getAllRole() : iterable {
        return $this->roleMapper->getAllRoles();
    }

    public function getTabularList() : array {
        $ret = [];
        $sn = 1;
        foreach($this->resourceActionList as $resourceName => $actions){
            $ret[] = [
                'SN' => $sn++,
                'resourceName' => $resourceName,
                'aclCount' => sizeof($this->groupPermissionMapper->getResourcePermissionsByClass($resourceName)),
                'identifier' => urlencode($resourceName)
            ];
        }
        return $ret;
    }
    
    public function getAcls(string $resourceName){
        return $this->groupPermissionMapper->getResourcePermissionsByClass($resourceName);
    }
    
    public function createAcl(string $resourceName, string $roleName, array $actions){
        try{
            $role = $this->roleMapper->getRoleWithName($roleName);
            $groupPermission = $this->groupPermissionMapper->create($role, $resourceName, $resourceName, $actions);
            $this->groupPermissionMapper->save($groupPermission);
            return true;
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
    
    public function deleteAcl(string $resourceName, string $roleName){
        $acls = $this->getAcls($resourceName);
        foreach($acls as $l){
            /*@var $l GroupPermissionInterface */
            if($l->getRole()->getName() == $roleName){
                $this->groupPermissionMapper->delete($l);
                break;
            }
        }
        return true;
    }
    
    public static function complieRoleNames(RoleInterface $r) : array {
        if(is_null($r->getParent())){
            return [$r->getName()];
        }
        return array_merge([$r->getName()],self::complieRoleNames($r->getParent()));
    }
    
    public static function isAllowedByHierarchy(RoleInterface $reference, RoleInterface $check) : bool {
        return in_array($reference->getName(), self::complieRoleNames($check));
    }
    
    public static function getListOfRolesAllowedByHierarchy(RoleInterface $reference, iterable $allRoles) : array {
        $ret = [];
        
        foreach($allRoles as $r){
            if(self::isAllowedByHierarchy($reference, $r)){
                $ret[$r->getName()] = $r->getName();
            }
        }
        if(in_array($reference->getName(),$ret)){
            unset($ret[$reference->getName()]);
        }
        return $ret;
    }
}
