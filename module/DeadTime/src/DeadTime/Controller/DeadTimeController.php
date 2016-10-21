<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace DeadTime\Controller;
use DeadTime\Model\ProductionDeadTime;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container as SessionContainer;
use Zend\Mvc\Controller\AbstractActionController;

use DeadTime\Form\DeadTimeForm;
use DeadTime\Model\Requisition;
use DeadTime\Lib\DeadTimeHelper;

class DeadTimeController extends AbstractActionController
{
    private $DeadCodeTable;
    private $DeadProblemTable;
    private $DeadCodeGroupTable;
    private $DeadSectionTable;
    private $ProductionDeadTimeTable;
    private $ProductionModelTable;
    private $RequisitionTable;
    private $ShiftTable;
    private $AreaTable;
    private $SessionBase;
    private $MachineId;
    private $IsGerente;
    private $IsOperador;
    private $IsAdmin;

    public function __construct()
    {
        $this->SessionBase = new SessionContainer('base');
        $this->IsGerente = $this->SessionBase->offsetGet('isGerente');
        $this->IsOperador = $this->SessionBase->offsetGet('isOperador');
        $this->IsAdmin = $this->SessionBase->offsetGet('isAdmin');
        $this->MachineId = $this->SessionBase->offsetGet('selMachineId');
    }

    public function indexAction()
    {
        return new ViewModel(array(
            'productionDeadTimes' => $this->getProductionDeadTimeTable()->fetchAll(),
        ));
    }

    public function listAction()
    {
        $production_model_id = (int)$this->params()->fromRoute('id', 0);
        if (!$production_model_id) {
            return $this->redirect()->toRoute('ProductionModel', array(
                'action' => 'index'
            ));
        }

        $codesForReq = $this->getDeadCodeTable()->getDeadCodesIdForRequisition();
        $records = array();

        if (count($codesForReq)) {
            foreach ($codesForReq as $result) {
                $records[] = $result;
            }
        }

        $productDeadTimes = $this->getProductionDeadTimeTable()->getProductionDeadTimeListByProdModelId($production_model_id);

        return new ViewModel(array(
            'productionDeadTimes' => $productDeadTimes,
            'productionModelId' => $production_model_id,
            'isGerenteUser' => $this->IsGerente,
            'isOperadorUser' => $this->IsOperador,
            'isAdminUser' => $this->IsAdmin,
            'codesForReq' => $records
        ));
    }

    public function populateDeadCodeGroupSelectByQuery($rs){
        foreach($rs as $value){
            $retArr[$value->id] = $value->name;
        }
        return $retArr;
    }

    public function populateDeadSectionSelectByQuery($rs){
        foreach($rs as $value){
            $retArr[$value->id] = $value->code . " - " .$value->name;
        }
        return $retArr;
    }

    public function populateDeadProblemSelectByQuery($rs){
        foreach($rs as $value){
            $retArr[$value->id] = $value->code . " - " . $value->description;
        }
        return $retArr;
    }

    public function addAction()
    {
        $form = new DeadTimeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $ProductionDeadTime = new ProductionDeadTime();
            $form->setInputFilter($ProductionDeadTime->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $ProductionDeadTime->exchangeArray($form->getData());

                // Revisar si este codigo muerto, es candidato a generar una requisicion
                $codesForReq = $this->getDeadCodeTable()->getDeadCodesIdForRequisition();
                $createRequisition = DeadTimeHelper::willCreateRequisition($ProductionDeadTime->death_code_id,$codesForReq);

                $prodDeadTimeId = $this->getProductionDeadTimeTable()->saveProductionDeadTime($ProductionDeadTime);

                // Si agregamos un tiempo muerto, y es candidato, asignamos una requisicion
                if($prodDeadTimeId && $createRequisition){

                    $area = $this->getAreaTable()->getAreaByMachineId($this->MachineId);
                    //Set Shift turn
                    $currHour = date('H:i:s');
                    $shiftTableRsArr = $this->getShiftTable()->getShiftByTime($currHour);

                    $Requisition = new Requisition();
                    $Requisition->id_machine = $this->MachineId;
                    $Requisition->id_area = $area->id;
                    $Requisition->id_shift = $shiftTableRsArr['number'];
                    $Requisition->machine_status = 'Maquina Parada';
                    $Requisition->id_dead_time = $prodDeadTimeId;

                    $this->getRequisitionTable()->saveRequisition($Requisition);

                }

                // Redirect to list of Production Model
                return $this->redirect()->toRoute('DeadTime', array(
                    'action' => 'list',
                    'id' => $ProductionDeadTime->production_model_id,
                ));
            }
        }
        else{
            $production_model_id = (int) $this->params()->fromRoute('id', 0);
            $form->get('production_model_id')->setValue($production_model_id);

            $deadCodeGroupDM = $this->getDeadCodeGroupTable()->fetchAllByMachineId();

            $populateArrSel = $this->populateDeadCodeGroupSelectByQuery($deadCodeGroupDM);
            $form->get('dead_code_group')->setValueOptions($populateArrSel);
        }

        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('ProductionModel', array(
                'action' => 'index'
            ));
        }
        $productionDeadTime = $this->getProductionDeadTimeTable()->getProductionDeadTime($id);

        // Si no tenemos un Tiempo muerto para este prod model, no editamos
        if(!$id)
        {
            return $this->redirect()->toRoute('ProductionModel', array(
                'action' => 'index'
            ));
        }

        $form  = new DeadTimeForm();
        $form->bind($productionDeadTime);
        $form->get('saveBtn')->setAttribute('value', 'Editar');

        $deadCodeGroupDM = $this->getDeadCodeGroupTable()->fetchAllByMachineId();

        $populateArrSel = $this->populateDeadCodeGroupSelectByQuery($deadCodeGroupDM);
        $form->get('dead_code_group')->setValueOptions($populateArrSel);

        // Por default vamos a poner el estatus del equipo como activo, ya que se supone que al editar se va liberar
        $form->get('machine_status')->setValue(1);

        // Si no tenemos subcodigo de tiempo muerto, no vamos a llenar la subagrupacion de codigo ni los subcodigos
        if(isset($productionDeadTime->death_problem_id) && $productionDeadTime->death_problem_id > 0){

            $DeadProblemDM = $this->getDeadProblemTable()->getProblemById($productionDeadTime->death_problem_id);
            $death_section_id = (int)$DeadProblemDM->death_section_id;

            // Seteamos el valor ddw de agrupacion basado en el death_code_id
            $form->get('death_section_id')->setValue($death_section_id);

            // Llenamos el valor ddw del subcodigo, pero este ya vienen en el modelo, entonces se setea solo.
            $DeadProblemDM = $this->getDeadProblemTable()->fetchAllBySectionId($death_section_id);
            $populateArrSel = $this->populateDeadProblemSelectByQuery($DeadProblemDM);
            $form->get('death_problem_id')->setValueOptions($populateArrSel);
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setInputFilter($productionDeadTime->getInputFilter());
            $form->setData($request->getPost());
            // Arreglando bug del bind para create_date, en zf2
            $create_date = $productionDeadTime->create_date;

            if ($form->isValid()) {
                $productionDeadTime->create_date = $create_date;

                $this->getProductionDeadTimeTable()->saveProductionDeadTime($form->getData());

                // Redireccionar a la lista de Modelos de Produccion
                return $this->redirect()->toRoute('DeadTime', array(
                    'action' => 'list',
                    'id' => $productionDeadTime->production_model_id
                ));
            }
        }

        return array(
            'form' => $form,
            'productionDeadTime' => $productionDeadTime
        );

    }

    public function shiftAction()
    {
        $shiftNumber = (int) $this->params()->fromRoute('id', 0);
        $dateUnix = (int) $this->params()->fromRoute('val', 0);

        $productDeadTimeDM = $this->getProductionDeadTimeTable()->getProductionDeadTimeListByShiftDate($shiftNumber,$dateUnix);

        return new ViewModel(array(
            'productionDeadTimes' => $productDeadTimeDM,
            'shiftNumber' => $shiftNumber,
            'dateUnix' => $dateUnix,
        ));
    }

    public function isAnInactiveDeadTimeAction()
    {
        $request = $this->getRequest();
        $prod_model_id = $request->getPost('prod_model_id');
        $productDeadTimeDM = $this->getProductionDeadTimeTable()->isAnInactiveDeadTime($prod_model_id);

        return new JsonModel(
            array('count' => $productDeadTimeDM->count)
        );

    }

    public function getDeadSectionAction()
    {
        $request = $this->getRequest();
        $dead_code_group_id = $request->getPost('dead_code_group_id');
        $DeadCodeSubgroupDM = $this->getDeadSectionTable()->fetchAllByParentCodeGroupId($dead_code_group_id);

        $a_json = array();
        $a_json_row = array();

        foreach($DeadCodeSubgroupDM as $DeadCodeSubgroup)
        {
            $a_json_row["id"] = $DeadCodeSubgroup->id;
            $a_json_row["code"] = $DeadCodeSubgroup->code;
            $a_json_row["description"] = $DeadCodeSubgroup->name;
            array_push($a_json, $a_json_row);
        }

        return new JsonModel(
            $a_json
        );

    }

    public function getDeadProblemAction()
    {
        $request = $this->getRequest();
        $death_code_id = $request->getPost('death_code_id');
        $DeadSubCodeDM = $this->getDeadProblemTable()->fetchAllByDeadCodeId($death_code_id);

        $a_json = array();
        $a_json_row = array();

        foreach($DeadSubCodeDM as $DeadSubCode)
        {
            $a_json_row["id"] = $DeadSubCode->id;
            $a_json_row["code"] = $DeadSubCode->code;
            $a_json_row["description"] = $DeadSubCode->description;
            array_push($a_json, $a_json_row);
        }

        return new JsonModel(
            $a_json
        );

    }

    public function canAddDeadTimeAction()
    {
        $request = $this->getRequest();
        $prod_model_id = $request->getPost('prod_model_id');
        $productionModelDM = $this->getProductionModelTable()->canAddDeadTime($prod_model_id);

        return new JsonModel(
            array('count' => $productionModelDM->count)
        );
    }

    // Ajax json
    public function getDeadCodeByGroupAction()
    {
        $request = $this->getRequest();
        $dead_code_group_id = $request->getPost('dead_code_group_id');

        $deadCodeGroupDM = $this->getDeadCodeTable()->fetchAllByDeadCodeGroup($dead_code_group_id);

        $a_json = array();
        $a_json_row = array();

        foreach($deadCodeGroupDM as $deadCodeGroup)
        {
            $a_json_row["id"] = $deadCodeGroup->id;
            $a_json_row["code"] = $deadCodeGroup->code;
            $a_json_row["description"] = $deadCodeGroup->description;
            array_push($a_json, $a_json_row);
        }

        return new JsonModel(
            $a_json
        );
    }

    public function getDeadCodeByMachineIdAction()
    {
        $deadCodeGroupDM = $this->getDeadCodeTable()->fetchAll();

        $a_json = array();
        $a_json_row = array();

        foreach($deadCodeGroupDM as $deadCodeGroup)
        {
            $a_json_row["id"] = $deadCodeGroup->id;
            $a_json_row["code"] = $deadCodeGroup->code;
            $a_json_row["description"] = $deadCodeGroup->description;
            array_push($a_json, $a_json_row);
        }

        return new JsonModel(
            $a_json
        );
    }

    public function getDeadCodeTable()
    {
        if (!$this->DeadCodeTable) {
            $sm = $this->getServiceLocator();
            $this->DeadCodeTable = $sm->get('DeadTime\Model\DeadCodeTable');
        }
        return $this->DeadCodeTable;
    }

    public function getDeadProblemTable()
    {
        if (!$this->DeadProblemTable) {
            $sm = $this->getServiceLocator();
            $this->DeadProblemTable = $sm->get('DeadTime\Model\DeadProblemTable');
        }
        return $this->DeadProblemTable;
    }

    public function getDeadCodeGroupTable()
    {
        if (!$this->DeadCodeGroupTable) {
            $sm = $this->getServiceLocator();
            $this->DeadCodeGroupTable = $sm->get('DeadTime\Model\DeadCodeGroupTable');
        }
        return $this->DeadCodeGroupTable;
    }

    public function getDeadSectionTable()
    {
        if (!$this->DeadSectionTable) {
            $sm = $this->getServiceLocator();
            $this->DeadSectionTable = $sm->get('DeadTime\Model\DeadSectionTable');
        }
        return $this->DeadSectionTable;
    }

    public function getProductionDeadTimeTable()
    {
        if (!$this->ProductionDeadTimeTable) {
            $sm = $this->getServiceLocator();
            $this->ProductionDeadTimeTable = $sm->get('DeadTime\Model\ProductionDeadTimeTable');
        }
        return $this->ProductionDeadTimeTable;
    }

    public function getProductionModelTable()
    {
        if (!$this->ProductionModelTable) {
            $sm = $this->getServiceLocator();
            $this->ProductionModelTable = $sm->get('ProductionModel\Model\ProductionModelTable');
        }
        return $this->ProductionModelTable;
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

    public function getShiftTable()
    {
        if (!$this->ShiftTable) {
            $sm = $this->getServiceLocator();
            $this->ShiftTable = $sm->get('ProductionModel\Model\ShiftTable');
        }
        return $this->ShiftTable;
    }




}
