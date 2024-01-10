<?php

namespace BplAdmin\Controller\UserManagement;

use BplAdmin\ModuleOpions\CrudOptions;
use BplAdmin\Contract\UserMapperInterface;
use CirclicalUser\Provider\AuthenticationProviderInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ListController extends AbstractActionController {

    public function __construct(
            private CrudOptions $options,
            private UserMapperInterface $userMapper,
            private AuthenticationProviderInterface $authMapper
    ) {}

    public function indexAction(): ViewModel {
        $pageNo = $this->params()->fromRoute('pageNo') !== NULL ? (int) $this->params()->fromRoute('pageNo') : 1;
        $startIndex = ($pageNo - 1) * $this->options->getItemsPerPage();
        $searchTerm = trim($this->params()->fromQuery('email')??'');
        $authRecords = [];
        $users = [];

        if ($searchTerm !== '') {
            $user = $this->userMapper->findByEmail($searchTerm);
            $allUsers = [$user];
            $totalRecordCount = is_object($user)?1:0;
        } else {
            $allUsers = $this->userMapper->getUsers($startIndex, $this->options->getItemsPerPage());
            $totalRecordCount = $this->userMapper->getTotalUserCount();
        }

        foreach ($allUsers as $u) {
            $authRecords[] = $this->authMapper->findByUserId($u->getId());
        }
        
        $noOfPages = (int) ceil($totalRecordCount / $this->options->getItemsPerPage());

        return new ViewModel([
            'users' => $allUsers,
            'authRecords' => $authRecords,
            'totalRecordCount' => $totalRecordCount,
            'noOfPages' => $noOfPages,
            'startIndex' => $startIndex,
            'currentPage' => $pageNo,
            'searchTerm' => ($searchTerm ==null?'':$searchTerm)
        ]);
    }

}
