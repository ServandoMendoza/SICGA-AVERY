<?php
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Db\TableGateway\TableGateway;

use Auth\Form\UserForm;
use Auth\Form\UserFilter;

class AdminController extends AbstractActionController
{
	protected $usersTable = null;
    private $usersTable2;

	// R - retrieve = Index
    public function indexAction()
    {
		return new ViewModel(array('rowset' => $this->getUsersTable()->select()));
	}

    public function listAction()
    {
        $this->layout('layout/layout_app');
        return new ViewModel(
            array()
        );
    }
	
	// C - Create
    public function createAction()
    {
		$form = new UserForm();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$form->setInputFilter(new UserFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$data = $form->getData();
				unset($data['submit']);
				if (empty($data['usr_registration_date'])) $data['usr_registration_date'] = '2013-07-19 12:00:00';
				$this->getUsersTable()->insert($data);
				return $this->redirect()->toRoute('auth/default', array('controller' => 'admin', 'action' => 'index'));													
			}			
		}
		return new ViewModel(array('form' => $form));
	}
	
	// U - Update
    public function updateAction()
    {
		$id = $this->params()->fromRoute('id');
		if (!$id) return $this->redirect()->toRoute('auth/default', array('controller' => 'admin', 'action' => 'index'));
		$form = new UserForm();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$form->setInputFilter(new UserFilter());
			$form->setData($request->getPost());
			 if ($form->isValid()) {
				$data = $form->getData();
				unset($data['submit']);
				if (empty($data['usr_registration_date'])) $data['usr_registration_date'] = '2013-07-19 12:00:00';
				$this->getUsersTable()->update($data, array('usr_id' => $id));
				return $this->redirect()->toRoute('auth/default', array('controller' => 'admin', 'action' => 'index'));													
			}			 
		}
		else {
			$form->setData($this->getUsersTable()->select(array('usr_id' => $id))->current());			
		}
		
		return new ViewModel(array('form' => $form, 'id' => $id));
	}
	
	// D - delete
    public function deleteAction()
    {
		$id = $this->params()->fromRoute('id');
		if ($id) {
			$this->getUsersTable()->delete(array('usr_id' => $id));
		}
		
		return $this->redirect()->toRoute('auth/default', array('controller' => 'admin', 'action' => 'index'));											
	}

    public function jsonListAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $table = 'users_vw';
        $primaryKey = 'usr_id';
        $sql_details = array(
            'user' => $config['db']['username'],
            'pass' => $config['db']['password'],
            'db'   => $config['db']['database'],
            'host' => $config['db']['host']
        );
        $columns = array(
            array( 'db' => 'usr_name', 'dt' => 0 ),
            array( 'db' => 'usrl_id',  'dt' => 1 ),
            array( 'db' => 'usr_registration_date',   'dt' => 2 ),
            array( 'db' => 'usr_id',   'dt' => 3 )
        );

        // Create an empty array for the encoded resultset
        $rows = array();

        $rs = $this->getUsersTable2()->getListJson($_GET, $sql_details, $table, $primaryKey, $columns);

        foreach($rs['data'] as $r)
        {
            $rows[] =  array_map('utf8_encode', $r);
        }

        $rs['data'] =$rows;

        // Remove View return
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent($_GET['callback'].'('.json_encode($rs).');');

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'text/html');

        return $response;
    }

    public function getUsersTable()
    {
        // I have a Table data Gateway ready to go right out of the box
        if (!$this->usersTable) {
            $this->usersTable = new TableGateway(
                'users',
                $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')
//				new \Zend\Db\TableGateway\Feature\RowGatewayFeature('usr_id') // Zend\Db\RowGateway\RowGateway Object
//				ResultSetPrototype
            );
        }
        return $this->usersTable;
    }

    // Creamos otro metodo, porque el creado por el programador de este codigo no mapeaba todos los nuevos metodos
    public function getUsersTable2()
    {
        if (!$this->usersTable2) {
            $sm = $this->getServiceLocator();
            $this->usersTable2 = $sm->get('Auth\Model\UsersTable');
        }
        return $this->usersTable2;
    }
}