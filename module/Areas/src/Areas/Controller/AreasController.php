<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Areas\Controller;

use Areas\Model\CellMachine;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use Areas\Form\AreaForm;
use Areas\Model\Area;
use Areas\Model\Cell;
use Areas\Model\SubArea;
use Zend\View\View;

class AreasController extends AbstractActionController
{
    private $MachineTable;
    private $AreaTable;
    private $CellTable;
    private $SubAreaTable;
    private $CellMachineTable;

    public function indexAction()
    {
        return new ViewModel();
    }

    public function createAction()
    {
        $this->layout('layout/layout_app');
        $form = new AreaForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $Area = new Area();
            $form->setInputFilter($Area->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $Area->exchangeArray($form->getData());
                $id_area = $this->getAreaTable()->saveArea($Area);

                // Redirect to list of Production Model
                return $this->redirect()->toRoute('Areas', array(
                    'action' => 'assignSubArea',
                    'id' => $id_area,
                ));
            }
        }

        return new ViewModel(
            array(
                'form' => $form
            )
        );
    }

    public function editAction()
    {
        $area_id = (int) $this->params()->fromRoute('id', 0);
        if (!$area_id) {
            return $this->redirect()->toRoute('ProductionModel', array(
                'action' => 'index'
            ));
        }

        $this->layout('layout/layout_app');
        $areaDM = $this->getAreaTable()->getAreaById($area_id);

        $form = new AreaForm();
        $form->bind($areaDM);
        $form->get('saveBtn')->setAttribute('value', 'Editar');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setInputFilter($areaDM->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $this->getAreaTable()->saveArea($form->getData());

                // Redirect to scrap list by production model
                return $this->redirect()->toRoute('Areas', array(
                    'action' => 'list'
                ));
            }
        }

        return array(
            'form' => $form,
            'area' => $areaDM
        );
    }

    public function assignSubAreaAction()
    {
        $this->layout('layout/layout_app');
        $id_area = (int)$this->params()->fromRoute('id', 0);
        if (!$id_area) {
            return $this->redirect()->toRoute('Application', array(
                'action' => 'index'
            ));
        }

        $areaDM = $this->getAreaTable()->getAreaById($id_area);

        if (empty($areaDM)) {
            return $this->redirect()->toRoute('Application', array(
                'action' => 'index'
            ));
        }

        $form = new AreaForm();

        $request = $this->getRequest();

        $form->get('id_area')->setValue($id_area);

        if ($request->isPost()) {
            $SubArea = new SubArea();
            $form->setInputFilter($SubArea->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $SubArea->exchangeArray($form->getData());
                $id_sub_area = $this->getSubAreaTable()->saveSubArea($SubArea);

                // Redirect to list of Production Model
                return $this->redirect()->toRoute('Areas', array(
                    'action' => 'createCells',
                    'id' => $id_sub_area,
                    'var' => 'area',
                    'val' => $id_area
                ));
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'id_area' => $id_area,
            )
        );
    }

    public function listAction()
    {
        $this->layout('layout/layout_app');
        $areasDM = $this->getAreaTable()->fetchAll();
        return new ViewModel(
            array(
                'areaslst' => $areasDM
            )
        );
    }

    public function detailAction()
    {
        $this->layout('layout/layout_app');
        $id_area = (int)$this->params()->fromRoute('id', 0);
        if (!$id_area) {
            return $this->redirect()->toRoute('Application', array(
                'action' => 'index'
            ));
        }

        $subAreaDetailDM = $this->getSubAreaTable()->getDetailByAreaId($id_area);

        //Get Sub Areas
        return new ViewModel(
            array(
                'subAreaDetailLst' => $subAreaDetailDM,
                'id_area' => $id_area
            )
        );

    }

    public function createCellsAction()
    {
        $this->layout('layout/layout_app');
        $id_sub_area = (int)$this->params()->fromRoute('id', 0);
        $id_area = (int)$this->params()->fromRoute('val', 0);

        if (!$id_sub_area) {
            return $this->redirect()->toRoute('Application', array(
                'action' => 'index'
            ));
        }

        $machineDM = $this->getMachineTable()->fetchAll();

        return new ViewModel(
            array(
                'machineList' => $machineDM,
                'id_sub_area' => $id_sub_area,
                'id_area' => $id_area
            )
        );
    }

    public function editSubAreaAction()
    {
        $this->layout('layout/layout_app');
        $id_sub_area = (int)$this->params()->fromRoute('id', 0);
        $id_area = (int)$this->params()->fromRoute('val', 0);
        if (!$id_sub_area) {
            return $this->redirect()->toRoute('Application', array(
                'action' => 'index'
            ));
        }

        $cellDM = $this->getSubAreaTable()->getCellsSubArea($id_sub_area);
        $machineDM = $this->getMachineTable()->fetchAll();

        $machine_cpy = array();

        foreach($machineDM as $machine){
            $machine_cpy[] = $machine;
        }

        $cellArr = array();
        $key = 0;

        foreach($cellDM as $cell){
            $CellMachineDM = $this->getCellMachineTable()->getCellMachineByCell($cell['id']);
            $cellArr[$key]["cell"] = $cell;

            foreach($CellMachineDM as $CellMachine) {
                $cellArr[$key]["cell_machine"][] = $CellMachine;
            }
            $key++;
        }

        return new ViewModel(
            array(
                'cellList' => $cellArr,
                'machineList' => $machine_cpy,
                'id_sub_area' => $id_sub_area,
                'id_area' => $id_area
            )
        );
    }

    // Ajax Function
    public function getCellMachineByCellAction()
    {
        $request = $this->getRequest();
        $id_cell = $request->getPost('id_cell');

        $CellMachineDM = $this->getCellMachineTable()->getCellsSubArea($id_cell);

        $a_json = array();
        $a_json_row = array();

        foreach($CellMachineDM as $CellMachine)
        {
            $a_json_row["id"] = $CellMachine->id;
            $a_json_row["name"] = $CellMachine->name;
            array_push($a_json, $a_json_row);
        }

        return new JsonModel(
            $a_json
        );
    }

    public function assignCellsAction()
    {
        $request = $this->getRequest();

        $infoArr = $request->getPost('info');
        $id_sub_area = $request->getPost('id_sub_area');
        $is_edit = $request->getPost('edit');

        $is_repeated = false;
        $deleted_cells = false;
        $success = false;

        foreach($infoArr as $info) {
            foreach ($info["machines"] as $id_machine) {
                $machineDM = $this->getMachineTable()->isMachineInArea($id_machine, $id_sub_area, $is_edit);

                if ($machineDM["count"] > 0) {
                    $is_repeated = true;
                    $repeated_machine_id = $id_machine;
                    break;
                }
            }

            if($is_repeated) break;
        }

        if(!$is_repeated) {
            foreach ($infoArr as $info) {
                if (!empty($is_edit) && !$deleted_cells) {
                    // Si es editar, limpiamos celdas y maquinas en las celdas y las volvemos a agregar nuevas.
                    $this->getCellMachineTable()->deleteCellMachineBySubArealId($id_sub_area);
                    $this->getCellTable()->deleteBySubAreaId($id_sub_area);
                    $deleted_cells = true;
                }

                $cell = new Cell();
                $cell->id_sub_area = (int)$id_sub_area;
                $cell->name = $info["name"];

                $id_cell = $this->getCellTable()->saveCell($cell);

                foreach ($info["machines"] as $machine_id) {
                    $cellMachine = new CellMachine();
                    $cellMachine->id_machine = (int)$machine_id;
                    $cellMachine->id_cell = (int)$id_cell;

                    $this->getCellMachineTable()->saveCellMachine($cellMachine);
                }

                $success = true;
            }
        }

        return new JsonModel(
            array('result' => $success, 'repeated_machine' => $repeated_machine_id)
        );
    }

    public function deleteSubAreaAction()
    {
        $request = $this->getRequest();
        $id_sub_area = $request->getPost('id');

        $this->getCellMachineTable()->deleteCellMachineBySubArealId($id_sub_area);
        $this->getCellTable()->deleteBySubAreaId($id_sub_area);
        $this->getSubAreaTable()->delete($id_sub_area);

        return new JsonModel(
            array(
                "result" => true
            )
        );
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

    public function getCellTable()
    {
        if (!$this->CellTable) {
            $sm = $this->getServiceLocator();
            $this->CellTable = $sm->get('Areas\Model\CellTable');
        }
        return $this->CellTable;
    }

    public function getSubAreaTable()
    {
        if (!$this->SubAreaTable) {
            $sm = $this->getServiceLocator();
            $this->SubAreaTable = $sm->get('Areas\Model\SubAreaTable');
        }
        return $this->SubAreaTable;
    }

    public function getCellMachineTable()
    {
        if (!$this->CellMachineTable) {
            $sm = $this->getServiceLocator();
            $this->CellMachineTable = $sm->get('Areas\Model\CellMachineTable');
        }
        return $this->CellMachineTable;
    }


    
}

