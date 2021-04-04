<?php

namespace BplAdmin\Controller\UserManagement;

use BplUser\Form\Register;
use BplUser\Provider\BplUserInterface;
use BplUser\Provider\BplUserServiceInterface;
use CirclicalUser\Service\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Math\Rand;

class RegisterController extends AbstractActionController {

    /**
     * @var \BplUser\Provider\BplUserServiceInterface
     */
    private $bplUserService;

    /**
     * @var \CirclicalUser\Service\AuthenticationService 
     */
    private $authenticationService;

    /**
     * @var \BplUser\Form\Register 
     */
    private $registrationForm;

    /**
     * @var \BplUser\Provider\BplUserInterface 
     */
    private $userEntity;

    public function __construct(
            BplUserServiceInterface $bplUserService,
            AuthenticationService $authenticationService,
            Register $registrationForm,
            BplUserInterface $userEntity
    ) {
        $this->bplUserService = $bplUserService;
        $this->authenticationService = $authenticationService;
        $this->registrationForm = $registrationForm;
        $this->userEntity = $userEntity;
    }

    public function indexAction(): ViewModel {
        $vm = new ViewModel();
        $created = false;
        $post = $this->getRequest()->getPost()->toArray();
        $this->registrationForm->bind($this->userEntity);
        $this->registrationForm->setData($post);

        if ($this->getRequest()->isPost() && $this->registrationForm->isValid()) {

            try {
                $this->userEntity = $this->bplUserService->register($this->userEntity);
            } catch (\Exception $ex) {
                $this->registrationForm->get('email')->setMessages([$ex->getMessage()]);
            }
        }

        if ($this->getRequest()->isPost() && $this->userEntity !== false && $this->registrationForm->isValid()) {
            try {
                $password = Rand::getString(10, 'abcdefghijklmnopqrstuvwxyz'
                                . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                                . '0123456789', true);

                if (isset($post['password'])) {
                    $password = $post['password'];
                }
                $this->authenticationService->registerAuthenticationRecord($this->userEntity, $this->userEntity->getEmail(), $password);
                $created = true;
            } catch (\Exception $ex) {
                $this->registrationForm->get('email')->setMessages([$ex->getMessage()]);
            }
        }

        $vm->setVariables([
            'form' => $this->registrationForm,
            'created' => $created
        ]);
        return $vm;
    }

}
