<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 9/29/14
 * Time: 11:11 AM
 */

namespace ProductionModel\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use ProductionModel\Form\StandardProductionForm;
use ProductionModel\Model\StandardProduction;

use Custom\FormHelpers;

class StandardProductionController extends AbstractActionController
{
    private $StandardProductionTable;
    private $MachineTable;

    public function indexAction()
    {
        $this->layout('layout/layout_app');
        return new ViewModel(array());
    }

    public function addAction(){
        $this->layout('layout/layout_app');
        $form = new StandardProductionForm();
        $request = $this->getRequest();

        $form->get('size')->setValueOptions(FormHelpers::halfIncrementArray(5));
        $form->get('crew_size')->setValueOptions(FormHelpers::IncrementArray(5));
        $form->get('indirect_crew')->setValueOptions(FormHelpers::halfIncrementArray(5));

        $machineDM = $this->getMachineTable()->fetchAll();

        $form->get('machine_id')->setValueOptions(FormHelpers::populateSelectByObjQuery($machineDM));

        if ($request->isPost()) {
            $StandardProduction = new StandardProduction();
            $form->setInputFilter($StandardProduction->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $StandardProduction->exchangeArray($form->getData());

                //Don't allow duplicate on this criteria...
                $isRepeated = $this->getStandardProductionTable()->isRepeatedSize(
                    array(
                        'machine_id' => $StandardProduction->machine_id,
                        'size' => $StandardProduction->size));

                if($isRepeated)
                {
                    throw new \Exception('This is a repeated record');
                }

                $this->getStandardProductionTable()->saveStandardProduction($StandardProduction);

                // Redirect to list of Dead Code Groups
                return $this->redirect()->toRoute('StandardProduction', array(
                    'action' => 'index',
                ));
            }

        }

        return array('form' => $form);
    }

    public function editAction()
    {
        $this->layout('layout/layout_app');
        // If no Id, we cant edit, escape from here
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('StandardProduction', array(
                'action' => 'index'
            ));
        }

        $standardProductionDM = $this->getStandardProductionTable()->getStandardProductionById($id);

        // If no Standard Production is available with this id, we escape from here
        if(!$standardProductionDM)
        {
            return $this->redirect()->toRoute('StandardProduction', array(
                'action' => 'index'
            ));
        }

        $form  = new StandardProductionForm();

        $form->bind($standardProductionDM);
        $form->get('saveBtn')->setAttribute('value', 'Editar');

        $request = $this->getRequest();

        if ($request->isPost()) {

            $StandardProduction = new StandardProduction();
            $form->setInputFilter($StandardProduction->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $this->getStandardProductionTable()->saveStandardProduction($form->getData());

                return $this->redirect()->toRoute('StandardProduction', array(
                    'action' => 'index',
                ));
            }
        }

        $form->get('size')->setValueOptions(FormHelpers::halfIncrementArray(5));
        $form->get('crew_size')->setValueOptions(FormHelpers::IncrementArray(5));
        $form->get('indirect_crew')->setValueOptions(FormHelpers::halfIncrementArray(5));

        $machineDM = $this->getMachineTable()->fetchAll();
        $form->get('machine_id')->setValueOptions(FormHelpers::populateSelectByObjQuery($machineDM));

        return array(
            'form' => $form,
            'standardProduction' => $standardProductionDM
        );
    }

    public function isRepeatedAjaxAction()
    {
        $request = $this->getRequest();
        $machine_id = $request->getPost('machine_id');
        $size = $request->getPost('size');

        $isRepeated = $this->getStandardProductionTable()->isRepeatedSize(
            array(
                'machine_id' => $machine_id,
                'size' => $size)
        );

        return new JsonModel(
            array('is_repeated' => $isRepeated)
        );
    }

    // Delete by Ajax
    public function deleteStandardProductionAjaxAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id');

        // Eliminar relaciones hacia scrap y tiempo muerto.
        $this->getStandardProductionTable()->deleteStandardProduction((int)$id);

        return new JsonModel(
            array(
                "result" => 1
            )
        );
    }

    public function jsonListAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $table = 'SICGA_Standard_Production';
        $primaryKey = 'id';
        $sql_details = array(
            'user' => $config['db']['username'],
            'pass' => $config['db']['password'],
            'db'   => $config['db']['database'],
            'host' => $config['db']['host']
        );
        $columns = array(
            array( 'db' => 'name', 'dt' => 0 ),
            array( 'db' => 'size', 'dt' => 1 ),
            array( 'db' => 'cycles_per_minute',  'dt' => 2 ),
            array( 'db' => 'products_per_hour',   'dt' => 3 ),
            array( 'db' => 'machine_runtime',   'dt' => 4 ),
            array( 'db' => 'crew_size',   'dt' => 5 ),
            array( 'db' => 'id',   'dt' => 6 ),
        );

        // Create an empty array for the encoded resultset
        $rows = array();

        $rs = $this->getStandardProductionTable()->getListJson($_GET, $sql_details, $table, $primaryKey, $columns);

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

    //Tables

    public function getStandardProductionTable()
    {
        if (!$this->StandardProductionTable) {
            $sm = $this->getServiceLocator();
            $this->StandardProductionTable = $sm->get('ProductionModel\Model\StandardProductionTable');
        }
        return $this->StandardProductionTable;
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