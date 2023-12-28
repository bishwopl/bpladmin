<?php

namespace BplAdmin\Controller\UserManagement;

use BplUser\Contract\BplUserInterface;
use CirclicalUser\Provider\AuthenticationProviderInterface;
use CirclicalUser\Provider\UserProviderInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class DeleteController extends AbstractActionController {

    /**
     * @var \CirclicalUser\Provider\UserProviderInterface
     */
    private $userMapper;

    /**
     *
     * @var \CirclicalUser\Provider\AuthenticationProviderInterface 
     */
    private $authMapper;

    public function __construct(UserProviderInterface $userMapper, AuthenticationProviderInterface $authMapper) {
        $this->userMapper = $userMapper;
        $this->authMapper = $authMapper;
    }

    public function indexAction(): ViewModel {
        $userId = $this->params()->fromRoute('user_id');
        
        $user = $this->userMapper->getUser($userId);
        
        if (!$user instanceof BplUserInterface) {
            throw new \Exception('User not found');
        }

        if ($this->getRequest()->isPost()) {
            $authRecord = $this->authMapper->findByUserId($userId);
            $this->userMapper->delete($user);
            $this->authMapper->delete($authRecord);
            $this->redirect()->toRoute('bpl-admin/user-management');
        }
        
        return new ViewModel([
            'user' => $user
        ]);
    }

}
