<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ScrapCode\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use ScrapCode\Form\ScrapCodeForm;

use ScrapCode\Model\ScrapCodeCodeTable;
use Zend\View\Model\JsonModel;
use ScrapCode\Model\ScrapCode;

use Custom\FormHelpers;

class ScrapCodeController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function addAction()
    {
        $this->layout('layout/layout_app');
        $request = $this->getRequest();
        $form = new ScrapCodeForm();

       $machineDM = $this->getMachineTable()->getMachinesWithDcGroup();

       $form->get('machine_id')->setValueOptions(FormHelpers::populateSelectByObjQuery($machineDM));

         if ($request->isPost()) {
            $ScrapCode = new ScrapCode($this->getServiceLocator());
            $form->setInputFilter($ScrapCode->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $ScrapCode->exchangeArray($form->getData());

                $this->getScrapCodeTable()->saveScrapCode($ScrapCode);

                // Redirect to list of ScrapCode ny production model
                return $this->redirect()->toRoute('ScrapCode', array(
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
        $ScrapCode_id = (int) $this->params()->fromRoute('id', 0);
        if (!$ScrapCode_id) {
            return $this->redirect()->toRoute('ScrapCode', array(
                'action' => 'list'
            ));
        }
        $ScrapCodeDM = $this->getScrapCodeTable()->getScrapCodeById($ScrapCode_id);

        $form  = new ScrapCodeForm();

        $machineDM = $this->getMachineTable()->getMachinesWithDcGroup();
        $form->get('machine_id')->setValueOptions(FormHelpers::populateSelectByObjQuery($machineDM));

        $form->bind($ScrapCodeDM);
        $form->get('saveBtn')->setAttribute('value', 'Editar');

       

        $request = $this->getRequest();
       

        if ($request->isPost()) {

            $form->setInputFilter($ScrapCodeDM->getInputFilter());
            $form->setData($request->getPost());
  
           
            if ($form->isValid()) {

                $this->getScrapCodeTable()->saveScrapCode($form->getData());

                // Redirect to scrap list by production model
                return $this->redirect()->toRoute('ScrapCode', array(
                    'action' => 'list',
                ));
            }
        }

        return array(
            'form' => $form,
            'ScrapCode' => $ScrapCodeDM
        );
    }

    public function jsonListAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $table = 'scrap_code_vw';
        $primaryKey = 'id';
        $sql_details = array(
            'user' => $config['db']['username'],
            'pass' => $config['db']['password'],
            'db'   => $config['db']['database'],
            'host' => $config['db']['host']
        );
        $columns = array(
            array( 'db' => 'code', 'dt' => 0 ),
            array( 'db' => 'description', 'dt' => 1 ),
            array( 'db' => 'machine_id',  'dt' => 2 ),
            array( 'db' => 'id',   'dt' => 3 ),
        );

        // Create an empty array for the encoded resultset
        $rows = array();

        $rs = $this->getScrapCodeTable()->getListJson($_GET, $sql_details, $table, $primaryKey, $columns);

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

    public function listAction()
    {
        $this->layout('layout/layout_app');
        $ScrapCodeDM = $this->getScrapCodeTable()->getScrapCode();

        return new ViewModel(array(
            'ScrapCodeList' => $ScrapCodeDM,
        ));
    }

    // Delete by Ajax
    public function deleteScrapCodeAjaxAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id');

        // Eliminar relaciones hacia scrap y tiempo muerto.
        $this->getScrapCodeTable()->deleteScrapCode((int)$id);

        return new JsonModel(
            array(
                "result" => 1
            )
        );
    }

    public function getScrapCodeTable()
    {
        if (!$this->ScrapCodeTable) {
            $sm = $this->getServiceLocator();
            $this->ScrapCodeTable = $sm->get('ScrapCode\Model\ScrapCodeTable');
        }
       
        return $this->ScrapCodeTable;
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

