<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 11/11/14
 * Time: 10:02 PM
 */

namespace DeadTime\Controller;

use DeadTime\Form\TechWorkForm;
use DeadTime\Model\TechWork;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use Custom\FormHelpers;
use Zend\Form\FormInterface;


class WorkController extends AbstractActionController{

    private $AreaTable;
    private $MachineTable;
    private $TechWorkTable;
    private $UsersTable;

    public function indexAction()
    {
        //$workList = $this->getTechWorkTable()->fetchAll();

        return new ViewModel(array(
            //'workList' => $workList
        ));
    }

    public function addAction()
    {
        $form = new TechWorkForm();
        $request = $this->getRequest();

        $areaDM = $this->getAreaTable()->fetchAll();
        $form->get('id_area')->setValueOptions(FormHelpers::populateSelectByObjQuery($areaDM, false));


        if ($request->isPost()) {
            $TechWork = new TechWork();

            $form->setInputFilter($TechWork->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $TechWork->exchangeArray($form->getData());

                $this->getTechWorkTable()->saveTechWork($TechWork);

                return $this->redirect()->toRoute('Work', array(
                    'action' => 'index'
                ));
            }
        }

        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function editAction()
    {
        $number = (int) $this->params()->fromRoute('id', 0);
        if (!$number && !is_numeric($number)) {
            return $this->redirect()->toRoute('Requisition', array(
                'action' => 'pendings'
            ));
        }

        $WorkDM = $this->getTechWorkTable()->fetchByNumber($number);

        $form = new TechWorkForm();
        $form->bind($WorkDM);
        $request = $this->getRequest();

        $areaDM = $this->getAreaTable()->fetchAll();
        $form->get('id_area')->setValueOptions(FormHelpers::populateSelectByObjQuery($areaDM, false));

        if ($request->isPost()) {

            $form->setInputFilter($WorkDM->getInputFilter());
            $form->setData($request->getPost());


            // TODO: investigar ( Temporal debido a que se esta borrando la info con el is valid.)
            $WorkClone = clone $WorkDM;

           // var_dump($form->isValid());exit;

            if ($form->isValid()) {

                $dateNow  = date('Y-m-d H:i:s');
                $post = $request->getPost();

                $WorkClone->total = (strtotime($dateNow) - strtotime($WorkClone->create_date))/60;

                if($WorkClone->stop_date != "") {
                    $stop_date = strtotime($WorkClone->stop_date);
                    $now = strtotime($dateNow);

                    $time = $now - $stop_date;

                    $WorkClone->total = $WorkClone->total + ($time/60);

                }

                $WorkClone->stop_date = $dateNow;
                $WorkClone->free = $post->free;

                $WorkClone->stopped = ($WorkClone->stopped) ?  0 : 1;

                $this->getTechWorkTable()->saveTechWork($WorkClone);

                return $this->redirect()->toRoute('Work', array(
                    'action' => 'edit', 'id' => $WorkClone->number
                ));

            }

        }

        return new ViewModel(array(
            'form' => $form,
            'WorkDM' => $WorkDM
        ));

    }


    public function workBreakAction()
    {
        $techWorkDM = $this->getTechWorkTable()->fetchAll();

        return new ViewModel(array(
            'techWorkList' => $techWorkDM
        ));
    }

    public function getMachineByAreaAction()
    {
        $request = $this->getRequest();
        $idArea = $request->getPost('id_area');

        $machineDM = $this->getMachineTable()->getMachineByAreaId($idArea);

        $a_json = array();
        $a_json_row = array();

        foreach($machineDM as $machine)
        {
            $a_json_row["id"] = $machine["id"];
            $a_json_row["name"] = $machine["name"];
            array_push($a_json, $a_json_row);
        }

        return new JsonModel(
            $a_json
        );
    }

    public function getTechByShiftAction()
    {
        $request = $this->getRequest();
        $idShift = $request->getPost('id_shift');

        $usersDM = $this->getUsersTable()->getTechByShift($idShift);

        $a_json = array();
        $a_json_row = array();

        foreach($usersDM as $users)
        {
            $a_json_row["id"] = $users->usr_id;
            $a_json_row["name"] = $users->usr_name;
            array_push($a_json, $a_json_row);
        }

        return new JsonModel(
            $a_json
        );
    }

    public function jsonListAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $table = 'tech_work_vw';
        $primaryKey = 'number';
        $sql_details = array(
            'user' => $config['db']['username'],
            'pass' => $config['db']['password'],
            'db'   => $config['db']['database'],
            'host' => $config['db']['host']
        );
        $columns = array(
            array( 'db' => 'number', 'dt' => 0 ),
            array( 'db' => 'type',  'dt' => 1 ),
            array( 'db' => 'total',   'dt' => 2 ),
            array( 'db' => 'machine_name',   'dt' => 3 ),
            array( 'db' => 'shift_name',   'dt' => 4 ),
            array( 'db' => 'usr_name',   'dt' => 5 ),
            array( 'db' => 'free',   'dt' => 6 ),
            array( 'db' => 'create_date',   'dt' => 7 )
        );

        // Create an empty array for the encoded resultset
        $rows = array();

        $rs = $this->getTechWorkTable()->getListJson($_GET, $sql_details, $table, $primaryKey, $columns);

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

    public function getAreaTable()
    {
        if (!$this->AreaTable) {
            $sm = $this->getServiceLocator();
            $this->AreaTable = $sm->get('Areas\Model\AreaTable');
        }
        return $this->AreaTable;
    }

    public function getUsersTable()
    {
        if (!$this->UsersTable) {
            $sm = $this->getServiceLocator();
            $this->UsersTable = $sm->get('Auth\Model\UsersTable');
        }
        return $this->UsersTable;
    }

    public function getTechWorkTable()
    {
        if (!$this->TechWorkTable) {
            $sm = $this->getServiceLocator();
            $this->TechWorkTable = $sm->get('DeadTime\Model\TechWorkTable');
        }
        return $this->TechWorkTable;
    }
} 