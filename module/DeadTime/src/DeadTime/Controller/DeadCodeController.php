<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 9/18/14
 * Time: 10:14 PM
 */

namespace DeadTime\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;

use DeadTime\Form\DeadCodeGroupForm;
use DeadTime\Form\DeadCodeForm;
use DeadTime\Model\DeadCodeGroup;
use DeadTime\Model\DeadCode;

use Custom\FormHelpers;

class DeadCodeController extends AbstractActionController{

    private $DeadCodeGroupTable;
    private $DeadCodeTable;
    private $MachineTable;
    private $DeadCodeGroupMachineTable;

    public function populateDeadCodeGroupSelectByQuery($rs){
        foreach($rs as $value){
            $retArr[$value->id] = $value->name;
        }
        return $retArr;
    }

    public function listGroupAction()
    {
        $this->layout('layout/layout_app');
        return new ViewModel();
    }

    public function listAction()
    {
        $this->layout('layout/layout_app');
        return new ViewModel();
    }

    public function addAction()
    {
        $this->layout('layout/layout_app');
        $form = new DeadCodeForm();
        $request = $this->getRequest();

        //Populate dead code group select
        $deadCodeGroupDM = $this->getDeadCodeGroupTable()->fetchAllByMachineId();
        $populateArrSel = $this->populateDeadCodeGroupSelectByQuery($deadCodeGroupDM);
        $form->get('death_code_group_id')->setValueOptions($populateArrSel);

        $machineDM = $this->getMachineTable()->getMachinesWithDcGroup();
        $form->get('machine_id')->setValueOptions(FormHelpers::populateSelectByObjQuery($machineDM, true, 'Todas las máquinas'));

        if ($request->isPost()) {
            $DeadCode = new DeadCode();
            $form->setInputFilter($DeadCode->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $DeadCode->exchangeArray($form->getData());

                $this->getDeadCodeTable()->saveDeadCode($DeadCode);

                // Redirect to list of Dead Code Groups
                return $this->redirect()->toRoute('DeadCode', array(
                    'action' => 'list',
                ));
            }

        }

        return array('form' => $form);
    }

    public function editAction()
    {
        $this->layout('layout/layout_app');
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('DeadCode', array(
                'action' => 'list'
            ));
        }

        $deadCodeDM = $this->getDeadCodeTable()->fetchAllById($id);

        // Si no tenemos un Tiempo muerto para este prod model, no editamos
        if(!$id)
        {
            return $this->redirect()->toRoute('DeadCode', array(
                'action' => 'list'
            ));
        }

        $form = new DeadCodeForm();
        $request = $this->getRequest();

        //Populate dead code group select
        $deadCodeGroupDM = $this->getDeadCodeGroupTable()->fetchAllByMachineId();
        $populateArrSel = $this->populateDeadCodeGroupSelectByQuery($deadCodeGroupDM);
        $form->get('death_code_group_id')->setValueOptions($populateArrSel);

        $machineDM = $this->getMachineTable()->getMachinesWithDcGroup();
        $form->get('machine_id')->setValueOptions(FormHelpers::populateSelectByObjQuery($machineDM, true, 'Todas las máquinas'));

        $form->bind($deadCodeDM);
        $form->get('saveBtn')->setAttribute('value', 'Editar');

        if ($request->isPost()) {
            $form->setInputFilter($deadCodeDM->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                //var_dump($form->getData());exit;
                $this->getDeadCodeTable()->saveDeadCode($form->getData());

                // Redirect to list of Dead Code Groups
                return $this->redirect()->toRoute('DeadCode', array(
                    'action' => 'list',
                ));
            }
        }

        $deadCodeGroupMachineDM = $this->getDeadCodeGroupMachineTable()->fetchAllByGroupId($deadCodeDM->death_code_group_id);

        return array('form' => $form,
            'deadCodeDM' => $deadCodeDM,
            'machine_loaded_id' => empty($deadCodeGroupMachineDM)? 0: $deadCodeGroupMachineDM->machine_id);
    }

    public function addGroupAction()
    {
        $this->layout('layout/layout_app');
        $form = new DeadCodeGroupForm();
        $request = $this->getRequest();

        $machineDM = $this->getMachineTable()->fetchAll();
        $form->get('machine_id')->setValueOptions(FormHelpers::populateSelectByObjQuery($machineDM, true, 'Todas las máquinas'));

        if ($request->isPost()) {
            $DeadCodeGroup = new DeadCodeGroup();
            $form->setInputFilter($DeadCodeGroup->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $DeadCodeGroup->exchangeArray($form->getData());

                $death_code_group_id = $this->getDeadCodeGroupTable()->saveDeadCodeGroup($DeadCodeGroup);
                $machine_id = (int)$form->get('machine_id')->getValue();

                // If dead_code inserted correctly, and machine is selected... assign to group
                if($death_code_group_id && $machine_id) {

                    //$this->getDeadCodeGroupMachineTable()->existantValue(array('death_code_group_id'));
                    $this->getDeadCodeGroupMachineTable()->saveDeadCodeGroupMachine(
                        array('death_code_group_id' => $death_code_group_id,
                        'machine_id' => (int)$form->get('machine_id')->getValue())
                    );
                }

                // Redirect to list of Dead Code Groups
                return $this->redirect()->toRoute('DeadCode', array(
                    'action' => 'listGroup',
                ));
            }
        }

        return array('form' => $form);
    }

    public function editGroupAction()
    {
        $this->layout('layout/layout_app');
        // If no Id, we cant edit, escape from here
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('ProductionModel', array(
                'action' => 'index'
            ));
        }

        $deadCodeGroup = $this->getDeadCodeGroupTable()->getDeadCodeGroupById($id);

        // If no production model is available with this id, we escape from here
        if(!$deadCodeGroup)
        {
            return $this->redirect()->toRoute('DeadCode', array(
                'action' => 'index'
            ));
        }

        $form  = new DeadCodeGroupForm();

        $machineDM = $this->getMachineTable()->fetchAll();
        $DeadCodeGroupMachineTableDM = $this->getDeadCodeGroupMachineTable()->fetchAllByGroupId($id);

        $form->get('machine_id')->setValueOptions(FormHelpers::populateSelectByObjQuery($machineDM, true, 'Todas las máquinas'));

        $form->bind($deadCodeGroup);
        $form->get('saveBtn')->setAttribute('value', 'Editar');
        $form->get('machine_id')->setValue($DeadCodeGroupMachineTableDM->machine_id);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($deadCodeGroup->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getDeadCodeGroupTable()->saveDeadCodeGroup($deadCodeGroup);

                return $this->redirect()->toRoute('DeadCode', array(
                    'action' => 'listGroup',
                ));
            }
        }

        return array(
            'form' => $form,
            'deadCodeGroup' => $deadCodeGroup,
        );

    }

    public function deleteDCAjaxAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id');

        $this->getDeadCodeTable()->deleteDeadCode($id);

        return new JsonModel(
            array(
                "result" => true
            )
        );
    }

    public function deleteDeadCodeGroupAjaxAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id');

        // Eliminar relaciones hacia scrap y tiempo muerto.
        $this->getDeadCodeGroupTable()->deleteDeadCodeGroup((int)$id);

        return new JsonModel(
            array(
                "result" => 1
            )
        );
    }


    public function deadCodeExistsAction()
    {
        $request = $this->getRequest();
        $code = $request->getPost('code');

        $exists = $this->getDeadCodeTable()->deadCodeExists($code);

        return new JsonModel(
            array(
                "exists" => $exists
            )
        );
    }

    public function getDeadCodeGroupByMachineAction()
    {
        $request = $this->getRequest();
        $machine_id = $request->getPost('machine_id');

        $deadCodeGroupDM = $this->getDeadCodeGroupTable()->getDeadCodeGroupByMachineId($machine_id);

        $a_json = array();
        $a_json_row = array();

        foreach($deadCodeGroupDM as $deadCodeGroup)
        {
            $a_json_row["id"] = $deadCodeGroup->id;
            $a_json_row["name"] = $deadCodeGroup->name;
            array_push($a_json, $a_json_row);
        }

        return new JsonModel(
            $a_json
        );
    }

    public function getDeadCodeGroupByNameAction()
    {
        $request = $this->getRequest();
        $name = $request->getPost('name');

        // Eliminar relaciones hacia scrap y tiempo muerto.
        $deadCodeGroupDM = $this->getDeadCodeGroupTable()->getDeadCodeGroupByName($name);

        return new JsonModel(
            array(
                "count" => $deadCodeGroupDM->count
            )
        );
    }


    public function jsonListgAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $table = 'dc_group_vw';
        $primaryKey = 'id';
        $sql_details = array(
            'user' => $config['db']['username'],
            'pass' => $config['db']['password'],
            'db'   => $config['db']['database'],
            'host' => $config['db']['host']
        );
        $columns = array(
            array( 'db' => 'name', 'dt' => 0 ),
            array( 'db' => 'description',  'dt' => 1 ),
            array( 'db' => 'create_by',   'dt' => 2 ),
            array( 'db' => 'id',   'dt' => 3 )
        );

        // Create an empty array for the encoded resultset
        $rows = array();

        $rs = $this->getDeadCodeGroupTable()->getListJson($_GET, $sql_details, $table, $primaryKey, $columns);

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

    public function jsonListAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $table = 'dead_code_vw';
        $primaryKey = 'id';
        $sql_details = array(
            'user' => $config['db']['username'],
            'pass' => $config['db']['password'],
            'db'   => $config['db']['database'],
            'host' => $config['db']['host']
        );
        $columns = array(
            array( 'db' => 'code', 'dt' => 0 ),
            array( 'db' => 'description',  'dt' => 1 ),
            array( 'db' => 'create_by',   'dt' => 2 ),
            array( 'db' => 'death_code_group_id',   'dt' => 3 ),
            array( 'db' => 'id',   'dt' => 4 )
        );

        // Create an empty array for the encoded resultset
        $rows = array();

        $rs = $this->getDeadCodeTable()->getListJson($_GET, $sql_details, $table, $primaryKey, $columns);

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

    public function getDeadCodeGroupTable()
    {
        if (!$this->DeadCodeGroupTable) {
            $sm = $this->getServiceLocator();
            $this->DeadCodeGroupTable = $sm->get('DeadTime\Model\DeadCodeGroupTable');
        }
        return $this->DeadCodeGroupTable;
    }

    public function getDeadCodeTable()
    {
        if (!$this->DeadCodeTable) {
            $sm = $this->getServiceLocator();
            $this->DeadCodeTable = $sm->get('DeadTime\Model\DeadCodeTable');
        }
        return $this->DeadCodeTable;
    }

    public function getMachineTable()
    {
        if (!$this->MachineTable) {
            $sm = $this->getServiceLocator();
            $this->MachineTable = $sm->get('Machine\Model\MachineTable');
        }
        return $this->MachineTable;
    }

    public function getDeadCodeGroupMachineTable()
    {
        if (!$this->DeadCodeGroupMachineTable) {
            $sm = $this->getServiceLocator();
            $this->DeadCodeGroupMachineTable = $sm->get('DeadTime\Model\DeadCodeGroupMachineTable');
        }
        return $this->DeadCodeGroupMachineTable;
    }
} 