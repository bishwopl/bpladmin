<?php

namespace BplAdmin\Controller;

use Laminas\Mvc\Controller\AbstractActionController;


class AdminController extends AbstractActionController {
    public function indexAction(): \Laminas\View\Model\ViewModel {
        return parent::indexAction();
    }
}