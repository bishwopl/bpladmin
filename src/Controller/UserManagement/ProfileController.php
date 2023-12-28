<?php

namespace BplAdmin\Controller\UserManagement;

use CirclicalUser\Provider\UserProviderInterface;
use BplUser\Contract\BplUserInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Form\Form ;

class ProfileController extends AbstractActionController {

    /**
     * @var \Laminas\Form\Form 
     */
    protected $changeProfileForm;

    /**
     * @var \CirclicalUser\Provider\UserProviderInterface
     */
    protected $userMapper;

    public function __construct(
            UserProviderInterface $userMapper,
            Form $changeProfileForm
    ) {
        $this->userMapper = $userMapper;
        $this->changeProfileForm = $changeProfileForm;
    }

    public function indexAction(): ViewModel {
        $edited = false;
        $userId = $this->params()->fromRoute('user_id');

        $user = $this->userMapper->getUser($userId);
        
        if (!$user instanceof BplUserInterface) {
            throw new \Exception('User not found');
        }
        
        $this->changeProfileForm->bind($user);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $this->changeProfileForm->setData($data);

            if ($this->changeProfileForm->isValid()) {
                $user = $this->changeProfileForm->getObject();
                $this->userMapper->update($user);
                $this->bpluser()->changeEmail($user, $user->getEmail());
                $edited = true;
            }
        }
        
        return new ViewModel([
            'user' => $user,
            'form' => $this->changeProfileForm,
            'edited' => $edited
        ]);
    }
}
