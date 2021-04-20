<?php

namespace BplAdmin\ModuleOpions;

use Laminas\Stdlib\AbstractOptions;

class AccessOptions extends AbstractOptions {

    private $aclFilename = 'bpladmin.acl.local.php';
    private $aclFileLocation;

    public function getAclFileLocation() {
        return $this->aclFileLocation;
    }

    public function setAclFileLocation($aclFileLocation) {
        $this->aclFileLocation = $aclFileLocation;
        return $this;
    }

    public function getAclFilename() {
        return $this->aclFilename;
    }

    public function setAclFilename($aclFilename) {
        $this->aclFilename = $aclFilename;
        return $this;
    }

}
