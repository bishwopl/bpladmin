<?php

namespace BplAdmin\Controller;

use BplAdmin\ModuleOpions\CrudOptions;
use BplCrud\Contract\CrudInterface;
use BplUser\Form\Register;
use BplUser\Form\ChangeProfile;
use BplUser\Provider\BplUserInterface;
use CirclicalUser\Provider\AuthenticationProviderInterface;
use CirclicalUser\Provider\UserInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Math\Rand;

class UserManagementController extends AbstractActionController {

    /**
     * @var \BplCrud\Contract\CrudInterface 
     */
    private $userService;

    /**
     * @var \BplAdmin\ModuleOpions\CrudOptions
     */
    private $options;

    /**
     * @var \BplUser\Form\Register 
     */
    private $registrationForm;
    
    /**
     * @var \BplUser\Form\ChangeProfile 
     */
    private $profileForm;
    
    /**
     * @var \BplUser\Provider\BplUserInterface 
     */
    private $userEntity;
    
    /**
     *
     * @var \CirclicalUser\Provider\AuthenticationProviderInterface 
     */
    private $authMapper;

    public function __construct(
            CrudOptions $options,
            CrudInterface $userService,
            Register $registrationForm,
            ChangeProfile $profileForm,
            BplUserInterface $userEntity,
            AuthenticationProviderInterface $authMapper
    ) {
        $this->userService = $userService;
        $this->options = $options;
        $this->registrationForm = $registrationForm;
        $this->userEntity = $userEntity;
        $this->authMapper = $authMapper;
        $this->profileForm = $profileForm;
    }

    public function indexAction(): ViewModel {
        $pageNo = $this->params()->fromRoute('id1');
        $pageNo = $pageNo !== NULL ? $pageNo : 1;
        $startIndex = ($pageNo - 1) * $this->options->getItemsPerPage();
        $authRecords = [];

        $users = $this->userService->read([], $startIndex, $this->options->getItemsPerPage(), ['id' => 'DESC']);

        foreach ($users as $u) {
            $authRecords[] = $this->authMapper->findByUserId($u->getId());
        }

        return new ViewModel([
            'users' => $users,
            'authRecords' => $authRecords,
            'totalRecordCount' => $this->userService->getTotalRecordCount([]),
            'noOfPages' => $this->userService->getNoOfPages([], $this->options->getItemsPerPage()),
            'startIndex' => $startIndex,
            'currentPage' => $pageNo
        ]);
    }

    public function addAction(): ViewModel {
        $vm = new ViewModel();
        $created = false;
        $post = $this->getRequest()->getPost()->toArray();
        $this->registrationForm->bind($this->userEntity);
        $this->registrationForm->setData($post);
        
        if ($this->getRequest()->isPost() && $this->registrationForm->isValid()) {
            
            try {
                $this->userService->create($this->userEntity);
            } catch (\Exception $ex) {
                $this->registrationForm->get('email')->setMessages([$ex->getMessage()]);
            }
        }

        if ($this->getRequest()->isPost() && $this->userEntity !== false) {
            try {
                $password = Rand::getString(10, 'abcdefghijklmnopqrstuvwxyz'
                                . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                                . '0123456789', true);;
                if (isset($post['password'])) {
                    $password = $post['password'];
                }
                $this->auth()->registerAuthenticationRecord($this->userEntity, $this->userEntity->getEmail(), $password);
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
        
        /*
        $created = false;
        $pass = '';
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();


            $this->userService->setData($data);

            if ($this->userService->isFormValid()) {
                $user = $this->userService->getObject();
                $this->userService->create($this->userService->getObject());
                $created = true;
            }
            if ($created && $user instanceof UserInterface) {
                $pass = Rand::getString(10, 'abcdefghijklmnopqrstuvwxyz'
                                . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                                . '0123456789', true);
                ;
                $this->auth()->create($user, $user->getEmail(), $pass);
            }
        }
        return new ViewModel([
            'form' => $this->userService->getForm(),
            'created' => $created,
            'message' => 'User created with default password : ' . $pass
        ]);
         * 
         */
    }

    public function changepasswordAction(): ViewModel {
        $created = false;
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $this->userService->setData($data);

            if ($this->userService->isFormValid()) {
                $this->userService->create($this->userService->getObject());
                $created = true;
            }
        }
        return new ViewModel([
            'form' => $this->userService->getForm(),
            'created' => $created
        ]);
    }

    public function changeprofileAction(): ViewModel {
        $edited = false;
        $id = $this->params()->fromRoute('id1');

        $userPaginator = $this->userService->read(['id' => $id]);
        if ($userPaginator->count() !== 1) {
            throw new \Exception('Role not found');
        }
        $userPaginator->getIterator()->seek(0);
        $user = $userPaginator->getIterator()->current();
        $this->userService->bind($user);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $this->userService->setData($data);

            if ($this->userService->isFormValid()) {
                $this->userService->update($this->userService->getObject());
                $edited = true;
            }
        }
        
        
        return new ViewModel([
            'form' => $this->profileForm,
            'edited' => $edited
        ]);
    }
    
    public function assignrolesAction(){
        return new ViewModel();
    }

    public function deleteAction(): ViewModel {
        $id = $this->params()->fromRoute('id1');
        $userPaginator = $this->userService->read(['id' => $id]);

        if ($userPaginator->count() !== 1) {
            throw new \Exception('User not found');
        }
        $userPaginator->getIterator()->seek(0);
        $user = $userPaginator->getIterator()->current();

        if ($this->getRequest()->isPost()) {
            $this->userService->delete($user);
            $authRecord = $this->authMapper->findByUserId($id);
            $this->authMapper->delete($authRecord);
            $this->redirect()->toRoute('bpl-admin/user-management');
        }
        return new ViewModel([
            'form' => $this->userService->getForm(),
            'user' => $user
        ]);
    }

}
