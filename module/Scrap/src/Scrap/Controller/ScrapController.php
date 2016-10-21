<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Scrap\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container as SessionContainer;

use Scrap\Form\ScrapForm;
use Scrap\Model\Scrap;
use Custom\Utilities;

class ScrapController extends AbstractActionController
{
    protected $ScrapCodeTable;
    protected $ScrapTable;
    protected $ProductionModel;
    protected $IsGerente;


    public function __construct()
    {
        $this->SessionBase = new SessionContainer('base');
        $this->IsGerente = $this->SessionBase->offsetGet('isGerente');
    }

    public function indexAction()
    {
        return array();
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form = new ScrapForm();

        if ($request->isPost()) {
            $Scrap = new Scrap();
            $form->setInputFilter($Scrap->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $Scrap->exchangeArray($form->getData());

                $prodModelDm = $this->getProductionModelTable()->getCrudeProductionModelById($Scrap->production_model_id);
                $actual_production = ($prodModelDm->actual_production * 1);

                // Si no tiene producion actual, no se puede capturar scrap.
                if($actual_production > 0){
                    $Scrap->percentage = Utilities::calculatePercentage($Scrap->quantity, $actual_production);
                    $this->getScrapTable()->saveScrap($Scrap);

                    // Redirect to scrap list by production model
                    return $this->redirect()->toRoute('Scrap', array(
                        'action' => 'list',
                        'id' => $Scrap->production_model_id
                    ));
                }
                else{
                    $this->flashMessenger()->addMessage('NO SE CAPTURO SCRAP: no existe produccion actual.');

                    return $this->redirect()->toRoute('Scrap', array(
                        'action' => 'add',
                        'id' => $Scrap->production_model_id,
                    ));
                }
            }
        }
        else{
            $production_model_id = (int) $this->params()->fromRoute('id', 0);
            if (!$production_model_id) {
                return $this->redirect()->toRoute('ProductionModel', array(
                    'action' => 'index'
                ));
            }

            $form->get('production_model_id')->setValue($production_model_id);
        }

        $scrapCodeDM = $this->getScrapCodeTable()->fetchAllByMachineId();
        $populateArrSel = $this->populateSelectByQuery($scrapCodeDM);
        $form->get('scrap_code_id')->setValueOptions($populateArrSel);

        return array(
            'form' => $form,
            'flashMessages' => $this->flashMessenger()->getMessages(),
            'scrap_codes' => $scrapCodeDM
        );
    }

    public function editAction()
    {
        $scrap_id = (int) $this->params()->fromRoute('id', 0);
        if (!$scrap_id) {
            return $this->redirect()->toRoute('ProductionModel', array(
                'action' => 'index'
            ));
        }
        $scrapDM = $this->getScrapTable()->getScrapById($scrap_id);

        $scrapCodeDM = $this->getScrapCodeTable()->fetchAllByMachineId();
        $populateArrSel = $this->populateSelectByQuery($scrapCodeDM);

        $form  = new ScrapForm();
        $form->bind($scrapDM);
        $form->get('saveBtn')->setAttribute('value', 'Editar');
        $form->get('scrap_code_id')->setValueOptions($populateArrSel);

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setInputFilter($scrapDM->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $prodModelDm = $this->getProductionModelTable()->getCrudeProductionModelById($scrapDM->production_model_id);
                $actual_production = ($prodModelDm->actual_production * 1);

                // Si no tiene producion actual, no se puede editar scrap.
                if($actual_production > 0) {
                    $form->getData()->percentage = Utilities::calculatePercentage($form->getData()->quantity, $actual_production);
                    $this->getScrapTable()->saveScrap($form->getData());

                    // Redirect to scrap list by production model
                    return $this->redirect()->toRoute('Scrap', array(
                        'action' => 'list',
                        'id' => $scrapDM->production_model_id
                    ));
                }
                else{
                    $this->flashMessenger()->addMessage('NO SE EDITO SCRAP: no existe produccion actual.');

                    return $this->redirect()->toRoute('Scrap', array(
                        'action' => 'add',
                        'id' => $scrapDM->production_model_id,
                    ));
                }
            }
        }

        return array(
            'form' => $form,
            'flashMessages' => $this->flashMessenger()->getMessages(),
            'scrap' => $scrapDM
        );
    }

    public function listAction()
    {
        $production_model_id = (int) $this->params()->fromRoute('id', 0);
        if (!$production_model_id) {
            return $this->redirect()->toRoute('ProductionModel', array(
                'action' => 'index'
            ));
        }

        $scrapDM = $this->getScrapTable()->getScrapByProdModelId($production_model_id);

        return new ViewModel(array(
            'scrapList' => $scrapDM,
            'productionModelId' => $production_model_id,
            'isGerenteUser' => $this->IsGerente,
        ));
    }

    public function shiftAction()
    {
        $shiftNumber = (int) $this->params()->fromRoute('id', 0);
        $dateUnix = (int) $this->params()->fromRoute('val', 0);

        $scrapDM = $this->getScrapTable()->getScrapByProdModelIdShift($shiftNumber, $dateUnix);

        return new ViewModel(array(
            'Scraps' => $scrapDM,
            'shiftNumber' => $shiftNumber,
            'dateUnix' => $dateUnix,
        ));
    }

    public function getScrapDescriptionAction(){
        $request = $this->getRequest();

        $scrap_code_id = $request->getPost('scrap_code_id');
        $scrapCodeDM = $this->getScrapCodeTable()->getScrapCodeById($scrap_code_id);

        return new JsonModel(
            array('scrap_code' => $scrapCodeDM)
        );

    }

    public function populateSelectByQuery($rs){
        foreach($rs as $value){
            $retArr[$value->id] = $value->code . ' - ' .$value->description;
        }
        return $retArr;
    }

    public function dailyScrapAction()
    {
        return new ViewModel(array());
    }

    public function jsonListAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $table = 'daily_scrap_vw';
        $primaryKey = 'product_sku';
        $sql_details = array(
            'user' => $config['db']['username'],
            'pass' => $config['db']['password'],
            'db'   => $config['db']['database'],
            'host' => $config['db']['host']
        );
        $columns = array(
            array( 'db' => 'product_sku', 'dt' => 0 ),
            array( 'db' => 'start_hour',  'dt' => 1 ),
            array( 'db' => 'end_hour',   'dt' => 2 ),
            array( 'db' => 'code',   'dt' => 3 ),
            array( 'db' => 'description',   'dt' => 4 ),
            array( 'db' => 'quantity',   'dt' => 5 ),
            array( 'db' => 'percentage',   'dt' => 6 ),
        );

        // Create an empty array for the encoded resultset
        $rows = array();

        $rs = $this->getScrapTable()->getListJson($_GET, $sql_details, $table, $primaryKey, $columns);

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

    public function getScrapCodeTable()
    {
        if (!$this->ScrapCodeTable) {
            $sm = $this->getServiceLocator();
            $this->ScrapCodeTable = $sm->get('Scrap\Model\ScrapCodeTable');
        }
        return $this->ScrapCodeTable;
    }

    public function getScrapTable()
    {
        if (!$this->ScrapTable) {
            $sm = $this->getServiceLocator();
            $this->ScrapTable = $sm->get('Scrap\Model\ScrapTable');
        }
       
        return $this->ScrapTable;
    }

    public function getProductionModelTable()
    {
        if (!$this->ProductionModel) {
            $sm = $this->getServiceLocator();
            $this->ProductionModel = $sm->get('ProductionModel\Model\ProductionModelTable');
        }
        return $this->ProductionModel;
    }
}
