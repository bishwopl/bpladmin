<?php

namespace BplAdmin\Controller\UserManagement;

use Laminas\Form\Form;
use CirclicalUser\Provider\UserProviderInterface;
use BplUser\Provider\BplUserInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class CredentialController extends AbstractActionController {

    /**
     * @var \CirclicalUser\Provider\UserProviderInterface
     */
    protected $userMapper;

    /**
     * @var \Laminas\Form\Form 
     */
    private $form;

    public function __construct(UserProviderInterface $userMapper, Form $form) {
        $this->userMapper = $userMapper;
        $this->form = $form;
    }

    public function indexAction(): ViewModel {
        $edited = false;
        $userId = $this->params()->fromRoute('user_id');

        $user = $this->userMapper->getUser($userId);
        if (!$user instanceof BplUserInterface) {
            throw new \Exception('User not found');
        }

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $this->form->setData($data);

            if ($this->form->isValid()) {
                $this->bpluser()->changePassword($user, $this->form->get('password')->getValue());
                $edited = true;
            }
        }
        
        return new ViewModel([
            'user' => $user,
            'form' => $this->form,
            'edited' => $edited
        ]);
    }

}
