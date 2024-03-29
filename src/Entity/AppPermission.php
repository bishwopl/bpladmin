<?php

namespace BplAdmin\Entity;

use BplAdmin\Entity\ControllerPermission;
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\Stdlib\ArraySerializableInterface;
use Laminas\Hydrator\ArraySerializableHydrator;

class AppPermission implements ArraySerializableInterface {

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection 
     */
    protected $controllerPermissions;

    private function __construct() {
        $this->controllerPermissions = new ArrayCollection();
    }

    public function getControllerPermissions(): ArrayCollection {
        return $this->controllerPermissions;
    }

    public static function cretaeFromConfig(array $moduleWiseGuardConfig): AppPermission {
        $appPermission = new AppPermission();

        foreach ($moduleWiseGuardConfig as $controllerConfig) {
            if (!array_key_exists('controllers', $controllerConfig)) {
                continue;
            }
            foreach ($controllerConfig['controllers'] as $controllerName => $controllerConfig) {

                $controllerPermission = ControllerPermission::createFromConfigArray($controllerName, $controllerConfig);
                $appPermission->addControllerPermission($controllerPermission);
            }
        }
        return $appPermission;
    }

    public function allowRolesForController(string $controllerName, array $roles = []): void {
        $controllerGaurd = $this->getControllerGaurd($controllerName);
        if ($controllerGaurd == null) {
            $controllerGaurd = ControllerPermission::createFromConfigArray($controllerName, []);
        } else {
            $key = $this->controllerPermissions->indexOf($controllerGaurd);
            $this->controllerPermissions->remove($key);
        }
        foreach ($roles as $role) {
            $controllerGaurd->addDefaultAllowedRole($role);
        }
        $this->addControllerPermission($controllerGaurd);
        return;
    }

    public function removeRolesForController(string $controllerName, array $roles = []): void {
        $controllerGaurd = $this->getControllerGaurd($controllerName);
        if ($controllerGaurd instanceof ControllerPermission) {
            $key = $this->controllerPermissions->indexOf($controllerGaurd);
            $this->controllerPermissions->remove($key);
            foreach ($roles as $role) {
                $controllerGaurd->removeDefaultAllowedRole($role);
            }
        }
        $this->addControllerPermission($controllerGaurd);
        return;
    }

    public function allowAllRolesToController(string $controllerName): void {
        $controllerGaurd = $this->getControllerGaurd($controllerName);
        if ($controllerGaurd == null) {
            $controllerGaurd = ControllerPermission::createFromConfigArray($controllerName, ['default' => []]);
        } else {
            $key = $this->controllerPermissions->indexOf($controllerGaurd);
            $this->controllerPermissions->remove($key);
            $controllerGaurd->setAllowAllByDefault(true);
        }
        $this->addControllerPermission($controllerGaurd);
        return;
    }

    public function removeAllRolesToController(string $controllerName): void {
        $controllerGaurd = $this->getControllerGaurd($controllerName);
        if ($controllerGaurd instanceof ControllerPermission) {
            $key = $this->controllerPermissions->indexOf($controllerGaurd);
            $this->controllerPermissions->remove($key);
            $controllerGaurd->setAllowAllByDefault(false);
            $controllerGaurd->removeAllDefaultRoles();
        }
        $this->addControllerPermission($controllerGaurd);
        return;
    }

    public function allowRolesToAccessAction(string $controllerName, string $actionName, array $roles): void {
        $controllerGaurd = $this->getControllerGaurd($controllerName);
        if ($controllerGaurd == null) {
            $controllerGaurd = ControllerPermission::createFromConfigArray($controllerName, []);
        } else {
            $key = $this->controllerPermissions->indexOf($controllerGaurd);
            $this->controllerPermissions->remove($key);
        }
        $controllerGaurd->allowRolesToAccessAction($actionName, $roles);
        $this->addControllerPermission($controllerGaurd);
        return;
    }

    public function removeRolesToAccessAction(string $controllerName, string $actionName, array $roles): void {
        $controllerGaurd = $this->getControllerGaurd($controllerName);
        if ($controllerGaurd instanceof ControllerPermission) {
            $key = $this->controllerPermissions->indexOf($controllerGaurd);
            $this->controllerPermissions->remove($key);

            $controllerGaurd->removeRolesToAccessAction($actionName, $roles);
            $this->addControllerPermission($controllerGaurd);
        }

        return;
    }

    public function toConfigArray(): array {
        $controllerConfig = [];
        foreach ($this->controllerPermissions as $c) {
            $controllerConfig = array_merge($controllerConfig, $c->toConfigArray());
        }
        
        foreach($controllerConfig as $controllerName => $config){
            if(sizeof($config) == 0){
                unset($controllerConfig[$controllerName]);
            }
        }

        return [
            'circlical' => [
                'user' => [
                    'guards' => [
                        'config-from-bpladmin' => [
                            'controllers' => $controllerConfig
                        ],
                    ],
                ],
            ],
        ];
    }
    
    public function toControllerWiseArray(): array {
        $controllerConfig = [];
        foreach ($this->controllerPermissions as $c) {
            $controllerConfig = array_merge($controllerConfig, $c->toConfigArray());
        }
        
        foreach($controllerConfig as $controllerName => $config){
            if(sizeof($config) == 0){
                unset($controllerConfig[$controllerName]);
            }
        }

        return $controllerConfig;
    }

    private function getControllerGaurd(string $controllerName): ?ControllerPermission {
        foreach ($this->controllerPermissions as $c) {
            if ($c->getControllerName() == $controllerName) {
                return $c;
            }
        }
        return null;
    }

    /**
     * Adds ControllerPermission object to collection
     * If already exists replace previous one
     * @param ControllerPermission $c
     * @return void
     */
    private function addControllerPermission(ControllerPermission $c): void {
        foreach ($this->controllerPermissions as $key => $check) {
            if ($c->getControllerName() == $check->getControllerName()) {
                $this->controllerPermissions->set($key, $c);
                return;
            }
        }
        $this->controllerPermissions->set($c->getControllerName(), $c);
        return;
    }

    public function getAllowedRoles(string $controllerName, string $actionName): ?array {
        $ret = [];
        $controller = $this->controllerPermissions->get($controllerName);
        if ($controller instanceof ControllerPermission) {
            $action = $controller->getActionPermissions()->get($actionName);
            if ($action instanceof ActionPermission) {
                $ret = $action->getAllowedRoles()->toArray();
            }
        }
        return $ret;
    }

    public function getDefaultControllerRoles(string $controllerName): ?array {
        $ret = [];
        $controller = $this->controllerPermissions->get($controllerName);
        if ($controller instanceof ControllerPermission) {
            if ($controller->getAllowAllByDefault() == true) {
                $ret = [];
            } elseif ($controller->getDefaultAllowedRoles()->count() > 0) {
                $ret = $controller->getDefaultAllowedRoles()->toArray();
            } else {
                $ret = null;
            }
        }
        return $ret;
    }

    /**
     * To be used for hydration using array serializable hydrator
     * @param array $array
     * @return void
     */
    public function exchangeArray(array $array): void {
        $hydrator = new ArraySerializableHydrator();
        foreach($array as $controllerConfig){
            if(!is_array($controllerConfig)){
                continue;
            }
            $controller = ControllerPermission::getEmptyObject();
            $hydrator->hydrate($controllerConfig, $controller);
            $this->addControllerPermission($controller);
        }
    }

    public function getArrayCopy(): array {
        return $this->controllerPermissions->map(function(ControllerPermission $c) {
            return $c->getArrayCopy();
        })->toArray();
    }
    
    public static function getEmptyObject() : AppPermission {
        return new AppPermission();
    }

}
