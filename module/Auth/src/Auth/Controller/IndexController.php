<?php
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Authentication\Result;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;

use Auth\Model\Auth;
use Auth\Form\AuthForm;
use Auth\Form\AloginForm;
use Zend\Session\Container;
use Custom\FormHelpers;


class IndexController extends AbstractActionController
{
    protected $usersTable;
    protected $MachineTable;

    public function indexAction()
    {
        $user = $this->identity();

        // Esta logeado, pero no tiene permisos
        if ($user) {
            //return $this->redirect()->toRoute('auth/default', array('controller' => 'index', 'action' => 'blocked'));
            return $this->redirect()->toRoute('auth/default', array('controller' => 'index', 'action' => 'alogin'));
        }

        // No esta logeado
        return $this->redirect()->toRoute('auth/default', array('controller' => 'index', 'action' => 'login'));
    }

    public function aloginAction()
    {
        $form = new AloginForm();
        $messages = null;

        $request = $this->getRequest();
        if ($request->isPost()) {

            $token = $this->getRequest()->getPost('token');
            $userDM = $this->getUsersTable()->isValidUserToken($token);

            if($userDM->usrl_id)
            {
                // Logout Override
                $auth = new AuthenticationService();

                if ($auth->hasIdentity()) {
                    $identity = $auth->getIdentity();
                }

                $auth->clearIdentity();
                $sessionManager = new \Zend\Session\SessionManager();
                $sessionManager->forgetMe();

                // Login Override
                $session = new Container('base');
                $controller = explode( '\\', $session->offsetGet('tController'));
                $session->offsetSet('tLogOverride', true);

                $sm = $this->getServiceLocator();
                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                $config = $this->getServiceLocator()->get('Config');
                $staticSalt = $config['static_salt'];

                $authAdapter = new AuthAdapter($dbAdapter,
                    'users', // there is a method setTableName to do the same
                    'usr_name', // there is a method setIdentityColumn to do the same
                    'usr_password', // there is a method setCredentialColumn to do the same
                    "MD5(CONCAT('$staticSalt', ?, usr_password_salt)) AND usr_active = 1" // setCredentialTreatment(parametrized string) 'MD5(?)'
                );
                $authAdapter
                    ->setIdentity($userDM->usr_name)
                    ->setCredential($userDM->raw_password)
                ;

                $auth = new AuthenticationService();
                // or prepare in the globa.config.php and get it from there. Better to be in a module, so we can replace in another module.
                // $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
                // $sm->setService('Zend\Authentication\AuthenticationService', $auth); // You can set the service here but will be loaded only if this action called.
                $result = $auth->authenticate($authAdapter);

                switch ($result->getCode()) {
                    case Result::FAILURE_IDENTITY_NOT_FOUND:
                        // do stuff for nonexistent identity
                        break;

                    case Result::FAILURE_CREDENTIAL_INVALID:
                        // do stuff for invalid credential
                        break;

                    case Result::SUCCESS:
                        $storage = $auth->getStorage();
                        $storage->write($authAdapter->getResultRowObject(
                            null,
                            'usr_password'
                        ));
                        break;

                    default:
                        // do stuff for other failure
                        break;
                }
                foreach ($result->getMessages() as $message) {
                    $messages .= "$message\n";
                }

                $messages['login_messages'] = $messages;

                if ($auth->hasIdentity()) {

                    $action = $session->offsetGet('tAction');
                    $id = $session->offsetGet('tId');

                    $route_parms = array();

                    if(!empty($action))
                        $route_parms['action'] = $action;
                    if(!empty($id))
                        $route_parms['id'] = $id;

                    $route = strtolower($controller[2]);

                    if($route == "index") {
                        $route = $controller[0];
                    }
                    else{
                        $route = $controller[2];
                    }

                    return $this->redirect()->toRoute($route, $route_parms);
                }
            }
            else{
                $messages['error'] = 'La clave no es válida';
            }
        }

        $messages['error'] = 'El usuario no tiene los permisos suficientes, utilize la clave de acceso rápido hacia el usuario correspondiente o inicie sesión con otro usuario';
        return new ViewModel(array('form' => $form, 'messages' => $messages));
    }

    public function loginAction()
	{
        $this->layout('layout/auth');

		$user = $this->identity();
		$form = new AuthForm();
		$messages = null;

        $machineDM = $this->getMachineTable()->fetchAll();
        $form->get('cprod-cbx')->setValueOptions(
            FormHelpers::populateSelectByObjQuery($machineDM)
        );

		$request = $this->getRequest();
        if ($request->isPost()) {

			$authFormFilters = new Auth();
            $form->setInputFilter($authFormFilters->getInputFilter());
			$form->setData($request->getPost());

            $session = new Container('base');

			 if ($form->isValid()) {

				$data = $form->getData();
				$sm = $this->getServiceLocator();
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

				$config = $this->getServiceLocator()->get('Config');
				$staticSalt = $config['static_salt'];

				$authAdapter = new AuthAdapter($dbAdapter,
										   'users', // there is a method setTableName to do the same
										   'usr_name', // there is a method setIdentityColumn to do the same
										   'usr_password', // there is a method setCredentialColumn to do the same
										   "MD5(CONCAT('$staticSalt', ?, usr_password_salt)) AND usr_active = 1" // setCredentialTreatment(parametrized string) 'MD5(?)'
										  );
				$authAdapter
					->setIdentity($data['usr_name'])
					->setCredential($data['usr_password'])
				;
				
				$auth = new AuthenticationService();
				// or prepare in the globa.config.php and get it from there. Better to be in a module, so we can replace in another module.
				// $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
				// $sm->setService('Zend\Authentication\AuthenticationService', $auth); // You can set the service here but will be loaded only if this action called.
				$result = $auth->authenticate($authAdapter);
				switch ($result->getCode()) {
					case Result::FAILURE_IDENTITY_NOT_FOUND:
					case Result::FAILURE_IDENTITY_NOT_FOUND:
						// do stuff for nonexistent identity
                        $messages .= "Las credenciales introducidas no son válidas";
						break;

					case Result::FAILURE_CREDENTIAL_INVALID:
						// do stuff for invalid credential
                        $messages .= "Las credenciales introducidas no son válidas";
						break;

					case Result::SUCCESS:
						$storage = $auth->getStorage();
						$storage->write($authAdapter->getResultRowObject(
							null,
							'usr_password'
						));
						$time = 1209600; // 14 days 1209600/3600 = 336 hours => 336/24 = 14 days
//						if ($data['rememberme']) $storage->getSession()->getManager()->rememberMe($time); // no way to get the session
						if ($data['rememberme']) {
							$sessionManager = new \Zend\Session\SessionManager();
							$sessionManager->rememberMe($time);
						}
						break;

					default:
						// do stuff for other failure
						break;
				}

                 if ($auth->hasIdentity()) {
                     // Set Machine ID selected
                     $session->offsetSet('selMachineId', $data['cprod-cbx']);
                     $session->offsetSet('selMachineName', $data['cprod-name']);

                     if($session->offsetGet('isDispatch')) {
                         return $this->redirect()->toRoute('Requisition', array(
                             'action' => 'pendings',
                         ));
                     }
                     else{
                         return $this->redirect()->toRoute('ProductionModel', array(
                             'action' => 'index',
                         ));
                     }
                 }
			 }
		}

		return new ViewModel(array('form' => $form, 'messages' => $messages));

	}
	
	public function logoutAction()
	{
		$auth = new AuthenticationService();
		// or prepare in the globa.config.php and get it from there
		// $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
		}			
		
		$auth->clearIdentity();
//		$auth->getStorage()->session->getManager()->forgetMe(); // no way to get the sessionmanager from storage
		$sessionManager = new \Zend\Session\SessionManager();
		$sessionManager->forgetMe();
		
		return $this->redirect()->toRoute('auth/default', array('controller' => 'index', 'action' => 'login'));		
	}

    public function blockedAction()
    {
        return new ViewModel(array());
    }

    public function getUsersTable()
    {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('Auth\Model\UsersTable');
        }
        return $this->usersTable;
    }

    public function getMachineTable()
    {
        if (!$this->MachineTable) {
            $sm = $this->getServiceLocator();
            $this->MachineTable = $sm->get('Machine\Model\MachineTable');
        }

        return $this->MachineTable;
    }
}