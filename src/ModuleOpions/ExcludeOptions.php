<?php

namespace BplAdmin\ModuleOpions;

use Laminas\Stdlib\AbstractOptions;

class ExcludeOptions extends AbstractOptions {

    private $roles;
    
    private $resources;

    public function getRoles() {
        return $this->roles;
    }

    public function setRoles($roles) {
        $this->roles = $roles;
        return $this;
    }
    
    public function getResources() {
        return $this->resources;
    }

    public function setResources($resources) {
        $this->resources = $resources;
        return $this;
    }

}

