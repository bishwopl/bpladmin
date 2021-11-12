<?php

namespace BplAdmin\Controller\UserManagement;

use BplAdmin\ModuleOpions\CrudOptions;
use CirclicalUser\Provider\AuthenticationProviderInterface;
use CirclicalUser\Provider\UserProviderInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ListController extends AbstractActionController {

    /**
     * @var \BplAdmin\ModuleOpions\CrudOptions
     */
    private $options;

    /**
     * @var \CirclicalUser\Provider\UserProviderInterface
     */
    private $userMapper;

    /**
     *
     * @var \CirclicalUser\Provider\AuthenticationProviderInterface 
     */
    private $authMapper;

    public function __construct(
            CrudOptions $options,
            UserProviderInterface $userMapper,
            AuthenticationProviderInterface $authMapper
    ) {
        $this->options = $options;
        $this->userMapper = $userMapper;
        $this->authMapper = $authMapper;
    }

    public function indexAction(): ViewModel {
        $pageNo = $this->params()->fromRoute('pageNo') !== NULL ? (int) $this->params()->fromRoute('pageNo') : 1;
        $startIndex = ($pageNo - 1) * $this->options->getItemsPerPage();
        $searchTerm = $this->params()->fromQuery('email');
        $authRecords = [];
        $users = [];

        $allUsers = $this->userMapper->getAllUsers();
        $totalRecordCount = sizeof($allUsers);

        if (trim($searchTerm) !== '') {
            $totalRecordCount = 0;
            foreach($allUsers as $u){
                if(strpos($u->getEmail(), $searchTerm) !== false){
                    if($totalRecordCount>=$startIndex && $totalRecordCount<$startIndex + $this->options->getItemsPerPage()){
                        $users[] = $u;
                    }
                    $totalRecordCount ++;
                }
            }
        } else {
            for ($i = $startIndex; $i < $startIndex + $this->options->getItemsPerPage(); $i++) {
                if (!isset($allUsers[$i])) {
                    break;
                }
                $users[] = $allUsers[$i];
            }
        }

        $noOfPages = (int) ceil($totalRecordCount / $this->options->getItemsPerPage());

        foreach ($users as $u) {
            $authRecords[] = $this->authMapper->findByUserId($u->getId());
        }

        return new ViewModel([
            'users' => $users,
            'authRecords' => $authRecords,
            'totalRecordCount' => $totalRecordCount,
            'noOfPages' => $noOfPages,
            'startIndex' => $startIndex,
            'currentPage' => $pageNo,
            'searchTerm' => ($searchTerm ==null?'':$searchTerm)
        ]);
    }

}
