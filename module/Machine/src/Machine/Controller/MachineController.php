<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Machine\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Machine\Form\MachineForm;

use Machine\Model\MachineCodeTable;
use Zend\View\Model\JsonModel;
use Machine\Model\Machine;



class MachineController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function addAction()
    {
        $this->layout('layout/layout_app');
        $request = $this->getRequest();
        $form = new MachineForm();

         if ($request->isPost()) {
            $Machine = new Machine($this->getServiceLocator());
            $form->setInputFilter($Machine->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $Machine->exchangeArray($form->getData());
               
                $this->getMachineTable()->saveMachine($Machine);

                // Redirect to list of Machine ny production model
                return $this->redirect()->toRoute('Machine', array(
                    'action' => 'list',
                ));
            } 
        }

        return array(
            'form' => $form
        );
    }


    public function editAction()
    {
        $this->layout('layout/layout_app');
        $machine_id = (int) $this->params()->fromRoute('id', 0);
        if (!$machine_id) {
            return $this->redirect()->toRoute('Machine', array(
                'action' => 'list'
            ));
        }
        $machineDM = $this->getMachineTable()->getMachineById($machine_id);

        $form  = new MachineForm();
        $form->bind($machineDM);
        $form->get('saveBtn')->setAttribute('value', 'Editar');

       

        $request = $this->getRequest();
       

        if ($request->isPost()) {

            $form->setInputFilter($machineDM->getInputFilter());
            $form->setData($request->getPost());
           
            if ($form->isValid()) {

                $this->getMachineTable()->saveMachine($form->getData());

                // Redirect to scrap list by production model
                return $this->redirect()->toRoute('Machine', array(
                    'action' => 'list',
                ));
            }
        }

        return array(
            'form' => $form,
            'machine' => $machineDM
        );
    }

    public function listAction()
    {
        $this->layout('layout/layout_app');
        $machineDM = $this->getMachineTable()->getMachines();

        return new ViewModel(array(
            'machineList' => $machineDM,
        ));
    }

    public function jsonListAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $table = 'SICGA_Machine';
        $primaryKey = 'id';
        $sql_details = array(
            'user' => $config['db']['username'],
            'pass' => $config['db']['password'],
            'db'   => $config['db']['database'],
            'host' => $config['db']['host']
        );
        $columns = array(
            array( 'db' => 'name', 'dt' => 0 ),
            array( 'db' => 'model', 'dt' => 1 ),
            array( 'db' => 'year',  'dt' => 2 ),
            array( 'db' => 'id',   'dt' => 3 ),
        );

        // Create an empty array for the encoded resultset
        $rows = array();

        $rs = $this->getMachineTable()->getListJson($_GET, $sql_details, $table, $primaryKey, $columns);

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

    public function getMachineTable()
    {
        if (!$this->MachineTable) {
            $sm = $this->getServiceLocator();
            $this->MachineTable = $sm->get('Machine\Model\MachineTable');
        }
       
        return $this->MachineTable;
    }
}

