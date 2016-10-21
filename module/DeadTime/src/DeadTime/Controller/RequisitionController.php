<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 11/5/14
 * Time: 7:52 PM
 */

namespace DeadTime\Controller;

use DeadTime\Form\ExportForm;
use DeadTime\Form\RequisitionForm;
use DeadTime\Model\RequisitionAssignment;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as SessionContainer;
use Custom\FormHelpers;
use Custom\Utilities;
use DeadTime\Model\Requisition;

class RequisitionController extends AbstractActionController{

    private $RequisitionTable;
    private $RequisitionAssignmentTable;
    private $AreaTable;
    private $UsersTable;
    private $MachineTable;
    private $IsGerente;

    public function __construct()
    {
        $this->SessionBase = new SessionContainer('base');
        $this->IsGerente = $this->SessionBase->offsetGet('isGerente');
    }

    public function indexAction()
    {
        $number = (int)$this->params()->fromRoute('id', 0);
        if (!$number) {
            return $this->redirect()->toRoute('Requisition', array(
                'action' => 'pendings'
            ));
        }

        $form = new RequisitionForm();
        $requisitionDM = $this->getRequisitionTable()->fetchByNumber($number);

        $areaDM = $this->getAreaTable()->fetchAll();
        $form->get('id_area')->setValueOptions(FormHelpers::populateSelectByObjQuery($areaDM, false));

        $machineDM = $this->getMachineTable()->fetchAll();
        $form->get('id_machine')->setValueOptions(FormHelpers::populateSelectByObjQuery($machineDM, false));

        $techUsersDM = $this->getUsersTable()->getTechUser();
        $form->get('id_tech')->setValueOptions(FormHelpers::populateSelectUsersByObjQuery($techUsersDM, true, 'Sin Asignar'));

        // Popula los tiempos de los tecnicos ABC
        $reqListDM = $this->getRequisitionAssignmentTable()->fetchAllByReqId($requisitionDM->number);

        // los tiempos se calculan cuando aun no ha sido liberada la requisicion
        foreach ($reqListDM as $req)
        {
            $reqCopy[] = $req;
        }

        $countReq = count($reqListDM);

        if ($requisitionDM->free == 0)
        {
            foreach ($reqCopy as $key => $req)
            {
                // Si este tecnico ya tiene tiempo acumulado saltar
                if($req->acc_time) {
                    $form->get('tech'.($key+1))->setValue($req->acc_time);
                    continue;
                }

                // Ajustar fecha real, si es el tecnico actual.
                if(($countReq-1) == $key)
                {
                    $req->assign_date = date("Y-m-d H:i:s");
                }

                // El tecnico A se compara contra la asiginacion inicial
                if ($key == 0)
                {
                    $assign_date = (strtotime($req->assign_date) - strtotime($requisitionDM->assign_time)) / 60;
                    $assign_date = number_format((float)$assign_date, 2, '.', '');

                    $form->get('tech1')->setValue($assign_date);
                } // El tecnico C se hace el calculo con el tiempo actual
                else if ($key == 2)
                {
                    //$assign_date = (strtotime(date("Y-m-d H:i:s")) - strtotime($reqCopy[$key - 1]->assign_date)) / 60;
                    $assign_date = (strtotime($req->assign_date) - strtotime($reqCopy[$key - 1]->assign_date)) / 60;

                    //Restar la cantidad de tiempo acumulada por el segundo técnico.
                    $assign_date = $assign_date - $reqCopy[$key - 1]->acc_time;

                    $assign_date = number_format((float)$assign_date, 2, '.', '');
                    $form->get('tech3')->setValue($assign_date);
                } // El Tecnico B es la resta del A - B
                else
                {
                    $assign_date = (strtotime($req->assign_date) - strtotime($reqCopy[$key - 1]->assign_date)) / 60;

                    //Restar la cantidad de tiempo acumulada por el primer técnico.
                    $assign_date = $assign_date - $reqCopy[$key - 1]->acc_time;

                    $assign_date = number_format((float)$assign_date, 2, '.', '');
                    $form->get('tech2')->setValue($assign_date);
                }
            }
        }
        // Si ya libero, ya calculamos con los valores guardados pertinentes a la requisicion
        else
        {
            foreach ($reqCopy as $key => $req)
            {
                $form->get('tech'.($key+1))->setValue($req->acc_time);
            }
        }

        $form->bind($requisitionDM);

        if(empty($requisitionDM->problem))
            $form->get('problem')->setValue($requisitionDM->other_problem);

        return new ViewModel(array(
            'form' => $form,
            'req_num' => $number,
            'requisition' => $requisitionDM,
            'techsCount' => $countReq,
            'flashMessages' => $this->flashMessenger()->getMessages(),
            'isGerenteUser' => $this->IsGerente,
        ));
    }

    public function modifyAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $Requisition = new Requisition();
            $form = new RequisitionForm();

            $form->setInputFilter($Requisition->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $Requisition->exchangeArray($form->getData());

                if(!$Requisition->assign_time)
                    $Requisition->assign_time = date("Y-m-d H:i:s");

                if($form->get('modify')->getValue() == 'free' ) {
                    // Tiempo actual
                    $dateNow = date("Y-m-d H:i:s");


                    $Requisition->free = 1;

                    if(empty($Requisition->fix_time))
                        $Requisition->fix_time = date("Y-m-d H:i:s");

                    // Guardamos los tiempos acumulados de los Tecnicos que hayan sido asginados (ABC)
                    $reqListDM = $this->getRequisitionAssignmentTable()->fetchAllByReqId($Requisition->number);

                    $req_count = count($reqListDM);
                    $i = 1;

                    foreach($reqListDM as $key => $req)
                    {
                        $req->acc_time = $form->get('tech'.($key+1))->getValue();
                        if($req_count == $i)
                        {
                            $time_diff = (strtotime($dateNow) - strtotime($req->assign_date)) / 60;
                            $req->acc_time += ($time_diff - $req->acc_time);
                        }

                        $this->getRequisitionAssignmentTable()->saveRequisitionAssignment($req);
                        $i++;
                    }

                    $this->flashMessenger()->addMessage('La requisicion ha sido liberada con exito');
                    $this->getRequisitionTable()->free($Requisition);
                }
                else{
                    $this->flashMessenger()->addMessage('La requisicion ha sido asignada con exito');

                    $id_req = $this->getRequisitionTable()->saveRequisition($Requisition);
                    $reqListDM = $this->getRequisitionAssignmentTable()->fetchAllByReqId($id_req);
                    $techCount = count($reqListDM);

                    // Guardar Técnico A,B,C
                    $RequisitionAssignment = new RequisitionAssignment();
                    $RequisitionAssignment->id = 0;
                    $RequisitionAssignment->id_requisition = $id_req;
                    $RequisitionAssignment->id_tech = $Requisition->id_tech;

                    // Guardamos los tiempos acumulados de los Tecnicos que hayan sido asginados (ABC)
                    $reqListDM = $this->getRequisitionAssignmentTable()->fetchAllByReqId($id_req);

                    foreach($reqListDM as $key => $req)
                    {
                        $req->acc_time = $form->get('tech'.($key+1))->getValue();
                        $this->getRequisitionAssignmentTable()->saveRequisitionAssignment($req);
                    }

                    if($techCount < 3)
                        $this->getRequisitionAssignmentTable()->saveRequisitionAssignment($RequisitionAssignment);
                }

                return $this->redirect()->toRoute('Requisition', array(
                    'action' => 'index',
                    'id' => $Requisition->number
                ));
            }
        }

        return new ViewModel(array());
    }

    public function pendingsAction()
    {
        //$requisitionDM = $this->getRequisitionTable()->getPendingList();

        return new ViewModel(array(
            //'pendings' => $requisitionDM
        ));
    }

    public function pendingspAction()
    {
        $requisitionDM = $this->getRequisitionTable()->fetchAllDetails();

        return new ViewModel(array(
            'requisitionList' => $requisitionDM
        ));
    }

    public function techsAction()
    {
        $id_shift = (int) $this->params()->fromRoute('id', 0);
        if (!$id_shift || !is_numeric($id_shift)) {
            $id_shift = 1;
        }

        $techList = $this->getUsersTable()->getTechsByShift($id_shift);

        return new ViewModel(array(
            'techList' => $techList,
            'shiftName' => Utilities::getShiftName($id_shift),
            'idShift' => $id_shift,
        ));
    }

    public function exportAction()
    {
        $form = new ExportForm();

        $request = $this->getRequest();

        if ($request->isPost()) {

            $form->setData($request->getPost());

            $start_date = $form->get('start_date')->getValue();
            $end_date = $form->get('end_date')->getValue();

            $rs = $this->getRequisitionTable()->exportRequisitionData($start_date, $end_date);

            return new ViewModel(array(
                'form' => $form,
            ));
        }

        return new ViewModel(array(
            'form' => $form,
        ));
    }

    public function exportPendingAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $post = $request->getPost();

            $start_date = $post->start_date;
            $end_date = $post->end_date;

            $pendings = $this->getRequisitionTable()->exportRequisitionData($start_date, $end_date);

            return new ViewModel(array(
                'pendings' => $pendings,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ));
        }
    }

    public function exporttAction()
    {
        $form = new ExportForm();

        $request = $this->getRequest();

        if ($request->isPost()) {

            $form->setData($request->getPost());

            $start_date = $form->get('start_date')->getValue();
            $end_date = $form->get('end_date')->getValue();

            $rs = $this->getRequisitionTable()->exportRequisitionData($start_date, $end_date);

            return new ViewModel(array(
                'form' => $form,
            ));
        }

        return new ViewModel(array(
            'form' => $form,
        ));
    }

    public function exportTechAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $post = $request->getPost();

            $start_date = $post->start_date;
            $end_date = $post->end_date;

            $techs = $this->getRequisitionTable()->exportTechData($start_date, $end_date);

            return new ViewModel(array(
                'techs' => $techs,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ));
        }
    }

    public function jsonListAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $table = 'pendings_vw';
        $primaryKey = 'number';
        $sql_details = array(
            'user' => $config['db']['username'],
            'pass' => $config['db']['password'],
            'db'   => $config['db']['database'],
            'host' => $config['db']['host']
        );
        $columns = array(
            array( 'db' => 'create_date', 'dt' => 0 ),
            array( 'db' => 'number',  'dt' => 1 ),
            array( 'db' => 'machine_name',   'dt' => 2 ),
            array( 'db' => 'tech_name',   'dt' => 3 ),
            array( 'db' => 'problem',   'dt' => 4 ),
            array( 'db' => 'generated_work',   'dt' => 5 ),
            array( 'db' => 'free',   'dt' => 6 ),
        );

        // Create an empty array for the encoded resultset
        $rows = array();

        $rs = $this->getRequisitionTable()->getListJson($_GET, $sql_details, $table, $primaryKey, $columns);

        foreach($rs['data'] as $r)
        {
            $rows[] =  array_map('utf8_encode', $r);
        }

        $rs['data'] =$rows;

        // Remove View return
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->getHeaders()->addHeaderLine('Content-Type', 'text/html; charset=utf-8');
        $response->setContent($_GET['callback'].'('.json_encode($rs).');');

        return $response;
    }

    public function isTechBusyAction()
    {
        $request = $this->getRequest();
        $id_tech = $request->getPost('id_tech');

        $usersDM = $this->getUsersTable()->isTechBusy($id_tech);

        $a_json = array();
        $a_json_row = array();

        foreach($usersDM as $user)
        {
            $a_json_row["count"] = $user["count"];
            array_push($a_json, $a_json_row);
        }

        return new JsonModel(
            $a_json
        );
    }

    public function getRequisitionTable()
    {
        if (!$this->RequisitionTable) {
            $sm = $this->getServiceLocator();
            $this->RequisitionTable = $sm->get('DeadTime\Model\RequisitionTable');
        }
        return $this->RequisitionTable;
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

    public function getMachineTable()
    {
        if (!$this->MachineTable) {
            $sm = $this->getServiceLocator();
            $this->MachineTable = $sm->get('Machine\Model\MachineTable');
        }
        return $this->MachineTable;
    }

    public function getRequisitionAssignmentTable()
    {
        if (!$this->RequisitionAssignmentTable) {
            $sm = $this->getServiceLocator();
            $this->RequisitionAssignmentTable = $sm->get('DeadTime\Model\RequisitionAssignmentTable');
        }
        return $this->RequisitionAssignmentTable;
    }
} 