<?php

namespace BplAdmin\ModuleOpions;

use Laminas\Stdlib\AbstractOptions;

class CrudOptions extends AbstractOptions {

    private $itemsPerPage;

    public function getItemsPerPage() {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage($itemsPerPage) {
        $this->itemsPerPage = $itemsPerPage;
        return $this;
    }

}
