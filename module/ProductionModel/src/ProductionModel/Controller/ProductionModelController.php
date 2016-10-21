<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ProductionModel\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container as SessionContainer;

use ProductionModel\Form\ProductionModelForm; 
use ProductionModel\Model\ProductionModel;
use DeadTime\Model\ProductionDeadTime;
use ProductionModel\Model\NoProgram;
use Custom\Utilities;

class ProductionModelController extends AbstractActionController
{
    protected $StandardProductionTable;
    protected $ProductionDeadTimeTable;
    protected $ShiftTable;
    protected $ProductTable;
    protected $ProductionModel;
    protected $NoProgramTable;
    protected $SessionBase;
    protected $IsAdmin;
    protected $IsGerente;
    protected $MachineId;
    protected $MachineName;

    public function __construct()
    {
        $this->SessionBase = new SessionContainer('base');
        $this->IsAdmin = $this->SessionBase->offsetGet('isAdmin');
        $this->IsGerente = $this->SessionBase->offsetGet('isGerente');
        $this->MachineId = $this->SessionBase->offsetGet('selMachineId');
        $this->MachineName = $this->SessionBase->offsetGet('selMachineName');
    }

    public function indexAction()
    {
        //Set Shift turn
        $currHour = date('H:i:s');
        $id_machine = $this->MachineId;
        $shiftTableRsArr = $this->getShiftTable()->getShiftByTime($currHour);
        $noProgramDM = $this->getNoProgramTable()->getLastByMachine($id_machine);

        return new ViewModel(array(
            'productionModels' => $this->getProductionModelTable()->getDayNowProductionModelsByShiftId($shiftTableRsArr['number']),
            'turnName' => $shiftTableRsArr['name'],
            'turnNumber' => $shiftTableRsArr['number'],
            'isAdminUser' => $this->IsAdmin,
            'machineName' => $this->MachineName,
            'noProgramDm' => $noProgramDM,
            'machineId' => $id_machine,
        ));
    }

    public function setNoProgramAction()
    {
        $request = $this->getRequest();
        $success = false;

        $noProgram = new NoProgram();
        $noProgram->id = $request->getPost('id');
        $noProgram->id_machine = $request->getPost('id_machine');
        $noProgram->is_active = $request->getPost('is_active');

        $idNoProgram = $this->getNoProgramTable()->save($noProgram);

        if($idNoProgram){
            $success = true;
        }

        return new JsonModel(
            array(
                'success' => $success,
                'update' => ($noProgram->id > 0),
                'id' => $idNoProgram
            )
        );
    }

    public function replaceAction()
    {
        $request = $this->getRequest();
        $form = new ProductionModelForm();

        // Populate sku prefix ddn
        $productSkuPrefixDM = $this->getProductTable()->getProductsSkuPrefix();
        $populateArrSel = $this->populateSelectSkuPrefixByQuery($productSkuPrefixDM);
        $form->get('sku_prefix')->setValueOptions($populateArrSel);

        if ($request->isPost()) {
            $ProductionModel = new ProductionModel();
            $form->setInputFilter($ProductionModel->getInputReplaceFilter());
            $form->setData($request->getPost());

            // TODO: Validation: Override for the moment
            if (!$form->isValid()) {
                $ProductionModel->exchangeArray($form->getData());

                $prod_model_id = $form->get('id')->getValue();

                $nowDate = new \DateTime('NOW');
                $nowHour = $nowDate->format('H:i:s');

                // Make a copy for child production model, and replace with child pertinent values
                $OriginalProductionModelDM = $this->getProductionModelTable()->getCrudeProductionModelById($prod_model_id);

                // Make a copy of parent production model to alter it
                $OriginalProductionModelUpdateDM = clone $OriginalProductionModelDM;

                $arrFilter = array(
                    "machine_id" => $this->MachineId,
                    "size" => (float)$OriginalProductionModelDM->sku_size
                );

                $OriginalProductionModelDM->replaced_prod_id = $OriginalProductionModelDM->id;
                $OriginalProductionModelDM->id = null;
                $OriginalProductionModelDM->program_time = date('i', strtotime($OriginalProductionModelDM->end_hour) - strtotime($nowHour));
                $OriginalProductionModelDM->product_sku =  $form->get('product_sku')->getValue();
                $OriginalProductionModelDM->start_hour = $nowHour;
                $OriginalProductionModelDM->create_date = $nowDate->format('Y-m-d H:i:s');
                $OriginalProductionModelDM->is_replace = 1;
                $OriginalProductionModelDM->actual_production = 0;
                $OriginalProductionModelDM->std_production = $this->getStdProd($arrFilter, $OriginalProductionModelDM->program_time);

                // Save replace production model
                $this->getProductionModelTable()->saveProductionModel($OriginalProductionModelDM);

                $OriginalProductionModelUpdateDM->program_time =  date('i', strtotime($nowHour));
                $OriginalProductionModelUpdateDM->std_production = $this->getStdProd($arrFilter, $OriginalProductionModelUpdateDM->program_time);
                $OriginalProductionModelUpdateDM->is_replace_parent = 1;
                $OriginalProductionModelUpdateDM->end_hour = $nowHour;
                $OriginalProductionModelUpdateDM->update_date = $nowDate->format('Y-m-d H:i:s');;

                //Update parent production model, with new program time and end time
                $this->getProductionModelTable()->saveProductionModel($OriginalProductionModelUpdateDM);

                // Redirect to list of Production Model
                return $this->redirect()->toRoute('ProductionModel');
            }
        }
        else{
            $production_model_id = (int) $this->params()->fromRoute('id', 0);
            if (!$production_model_id) {
                return $this->redirect()->toRoute('ProductionModel', array(
                    'action' => 'index'
                ));
            }

            $OriginalProductionModelDM = $this->getProductionModelTable()->getCrudeProductionModelById($production_model_id);
        }

        return array(
            'form' => $form,
            'productionModelId' => $production_model_id,
            'orgProductionModel' => $OriginalProductionModelDM,
        );
    }

    public function populateSelectByQuery($rs){

        $retArr[-1] = "Seleccione...";

        foreach($rs as $value){
            $retArr[$value->size] = 'Size: '.$value->size . ' - Cycles Per Min: ' . $value->cycles_per_minute ;
        }
        return $retArr;
    }

    public function populateSelectSkuPrefixByQuery($rs){
        foreach($rs as $value){
            $retArr[$value->sku_prefix] = $value->sku_prefix;
        }
        return $retArr;
    }

    public function addAction()
    {
        $form = new ProductionModelForm();
        $form->get('guardarBtn')->setValue('Agregar');

        // Checar Si tiene programa
        $noProgramDM = $this->getNoProgramTable()->getLastByMachine($this->MachineId);

        //var_dump($noProgramDM);

        //Set Shift turn 
        $currHour = date('H:i:s');
        $shiftTableRsArr = $this->getShiftTable()->getShiftByTime($currHour);

        $form->get('shift_now')->setValue($shiftTableRsArr['number']);

        $productSkuPrefixDM = $this->getProductTable()->getProductsSkuPrefix();
        $populateArrSel = $this->populateSelectSkuPrefixByQuery($productSkuPrefixDM);
        $form->get('sku_prefix')->setValueOptions($populateArrSel);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $ProductionModel = new ProductionModel();
            $form->setInputFilter($ProductionModel->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $ProductionModel->exchangeArray($form->getData());
                $this->getProductionModelTable()->saveProductionModel($ProductionModel);

                // Redirect to list of Production Model
                return $this->redirect()->toRoute('ProductionModel');
            }
        }

        return array(
            'form' => $form,
            'shift_id' => $shiftTableRsArr['number'],
            'no_program_dm' => $noProgramDM,
        );
    }

    public function editAction()
    {
        // If no Id, we cant edit, escape from here
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('ProductionModel', array(
                'action' => 'index'
            ));
        }

        $productionModel = $this->getProductionModelTable()->getProductionModelById($id);

        // If no production model is available with this id, we escape from here
        if(!$productionModel)
        {
            return $this->redirect()->toRoute('ProductionModel', array(
                'action' => 'index'
            ));
        }

        // If actual production is captured, we cant edit this production model anymore
        if((int)$productionModel->actual_production > 0 && !$this->IsAdmin)
        {
            return $this->redirect()->toRoute('ProductionModel', array(
                'action' => 'index'
            ));
        }

        $form  = new ProductionModelForm();

        $productSkuPrefixDM = $this->getProductTable()->getProductsSkuPrefix();
        $populateArrSel = $this->populateSelectSkuPrefixByQuery($productSkuPrefixDM);
        $form->get('sku_prefix')->setValueOptions($populateArrSel);

        $form->bind($productionModel);
        $form->get('guardarBtn')->setAttribute('value', 'Editar');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($productionModel->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getProductionModelTable()->saveProductionModel($form->getData());

                return $this->redirect()->toRoute('ProductionModel', array(
                    'action' => 'index',
                ));
            }
        }

        return array(
            'form' => $form,
            'productionModel' => $productionModel,
            'isAdminUser' => $this->IsAdmin,
            'isGerenteUser' => $this->IsGerente,
        );

    }

    // Ajax Get Standard Production by
    // Return type json
    public function geProductionFilteredPaginationAction()
    {
        $request = $this->getRequest();

        $filter_date = $request->getPost('filter_date');
        $filter_shift = $request->getPost('filter_shift');

        $productionModelDM = $this->getProductionModelTable()->getProductionModelsFilterPagination($filter_date,$filter_shift);

        return new JsonModel(
            $productionModelDM
        );
    }

    public function getStdProd($arrFilter, $program_time)
    {
        $StandardProductionDM = $this->getStandardProductionTable()->getStandardProductionByFilter($arrFilter);

        $machineCyclesPm = $StandardProductionDM->cycles_per_minute;
        $machineProductsPh = $StandardProductionDM->products_per_hour;

        return ($machineCyclesPm > 0)? ($program_time * $machineProductsPh) / 60 : 0;
    }

    // Ajax Get Standard Production by 
    // Return type json
    public function getStdProductionAction()
    {
        $request = $this->getRequest();

        $program_time = $request->getPost('program_time');
        $sku_size = $request->getPost('sku_size');

        $arrFilter = array(
            "machine_id" => $this->MachineId,
            "size" => (float)$sku_size
        );

        $StandardProductionDM = $this->getStandardProductionTable()->getStandardProductionByFilter($arrFilter);

        $machineCyclesPm = $StandardProductionDM->cycles_per_minute;
        $machineProductsPh = $StandardProductionDM->products_per_hour;

        $machineStdProd = ($machineCyclesPm > 0)? ($program_time * $machineProductsPh) / 60 : 0;

        $arrReturn = array(
            "stdProduction" => $machineStdProd,
            "machine_runtime" => $StandardProductionDM->machine_runtime
        );

        return new JsonModel(
            $arrReturn 
        );

    }

    public function isProductionShiftCapturedAction()
    {
        $request = $this->getRequest();

        $start_hour = $request->getPost('start_hour');
        $end_hour = $request->getPost('end_hour');

        $dateTime = date('Y-m-d');

        $arrFilter = array (
            'start_hour' => $start_hour,
            'end_hour' => $end_hour,
            'date_now' => $dateTime,
            'machine_id' => $this->MachineId
        );

        $StandardProductionDM = $this->getProductionModelTable()->getProductionModelByShiftFilter($arrFilter);

        return new JsonModel(
            $StandardProductionDM
        );
    }

    public function hasReplaceModelAction()
    {
        $request = $this->getRequest();
        $product_model_id = $request->getPost('product_model_id');
        $productModelDm = $this->getProductionModelTable()->hasReplaceModel($product_model_id);

        return new JsonModel(
            array('count' => $productModelDm->count)
        );
    }

    public function isValidSkuAction()
    {
        $request = $this->getRequest();
        $product_sku = $request->getPost('product_sku');
        $productDm = $this->getProductTable()->getProductById($product_sku);

        return new JsonModel(
            array('sku' => $productDm->sku)
        );
    }

    public function getProductSizeAction()
    {
        $request = $this->getRequest();
        $product_sku = $request->getPost('product_sku');
        $productDm = $this->getProductTable()->getProductById($product_sku);

        return new JsonModel(
            array('p_sku_size' => $productDm->size)
        );

    }

    public function getProductionHoursAction()
    {
        $request = $this->getRequest();
        $shiftId = $request->getPost('shift_id');
        $shiftDM = $this->getShiftTable()->getShiftById((int)$shiftId);

        $number = $shiftDM->number;

        switch($number)
        {
            case '1':
                    $arrProdHours = array( 
                        '06:55-07:55',
                        '07:55-08:55',
                        '08:55-09:55',
                        '09:55-10:55',
                        '10:55-11:55',
                        '11:55-12:55',
                        '12:55-13:55',
                        '13:55-14:55',
                        '14:55-15:25',
                    );
                break;

            case '2':
                    $arrProdHours = array( 
                        '15:25-16:25',
                        '16:25-17:25',
                        '17:25-18:25',
                        '18:25-19:25',
                        '19:25-20:25',
                        '20:25-21:25',
                        '21:25-22:25',
                        '22:25-23:25'
                    );
                break;

            case '3':
                    $arrProdHours = array( 
                        '23:25-00:25',
                        '00:25-01:25',
                        '01:25-02:25',
                        '02:25-03:25',
                        '03:25-04:25',
                        '04:25-05:25',
                        '05:25-06:25',
                        '06:25-06:55',
                    );
                break;
        }

        // Seccion para recortar los modelos no capturados en turno
        if(!$this->IsAdmin) {
            $time = date("H:i");
            $time_exploded = explode(':', $time);
            $time_sec_elapse = (($time_exploded[0] * 60) + 25);

            $count = count($arrProdHours);
            $i_now = 0;

            for ($i = 0; $i < $count; $i++) {

                // Para el caso de tercer truno que juega en dos dias el primer modelo
                if (($time_exploded[0] == '23' || $time_sec_elapse < 25) && $number == '3') {
                    $i_now = 0;
                    break;
                }

                list($start_hr, $end_hr) = explode('-', $arrProdHours[$i]);

                $big_than_sh = Utilities::isGreaterTime($start_hr, $time);
                $sh_equals = ($time == $start_hr);
                $low_than_eh = !Utilities::isGreaterTime($end_hr, $time);
                $eh_equals = ($time == $end_hr);

                if (($big_than_sh || $sh_equals) && ($low_than_eh && !$eh_equals)) {
                    $i_now = $i;
                    break;
                }
            }

            array_splice($arrProdHours, 0, $i_now);
        }

        $arrReturn = array(
            "prodHours" => $arrProdHours
        );

        return new JsonModel(
            $arrReturn 
        );
    }

    public function addByAjaxAction()
    {
        $request = $this->getRequest();

        $ProductionModel = new ProductionModel();

        $ProductionModel->shift_id = $request->getPost('shift_id');
        $ProductionModel->product_sku = $request->getPost('product_sku');
        $ProductionModel->sku_size = $request->getPost('sku_size');
        $ProductionModel->std_production = $request->getPost('std_production');
        $ProductionModel->start_hour = $request->getPost('start_hour');
        $ProductionModel->end_hour = $request->getPost('end_hour');
        $ProductionModel->shift_id = $request->getPost('shift_now');
        $ProductionModel->program_time = $request->getPost('program_time');

        $this->getProductionModelTable()->saveProductionModel($ProductionModel);

        $arrReturn = array(
            "result" => 1
        );

        return new JsonModel(
            $arrReturn
        );
    }

    public function getProductsByShiftAjaxAction()
    {
        //Set Shift turn
        $currHour = date('H:i:s');
        $shiftTableRsArr = $this->getShiftTable()->getShiftByTime($currHour);

        return new JsonModel(
            $this->getProductionModelTable()->getDayNowProductionModelsByShiftId($shiftTableRsArr['number'])
        );
    }

    public function getProductsAction()
    {
        $request = $this->getRequest();
        $term = $request->getQuery('term');
        $sku_prefix = $request->getQuery('sku_prefix');

        $StandardProductionDM = $this->getProductTable()->getProductionsPlanBySkuTerm($sku_prefix.$term);

        $a_json = array();
        $a_json_row = array();

        foreach($StandardProductionDM as $StandardProduction)
        {
            $a_json_row["value"] = $StandardProduction->sku;
            $a_json_row["size"] = $StandardProduction->size;
            array_push($a_json, $a_json_row);
        }

        return new JsonModel(
            $a_json 
        );
    }

    // Delete by Ajax
    public function deleteProductionModelAjaxAction()
    {
        $request = $this->getRequest();
        $prod_model_id = $request->getPost('prod_model_id');

        // Eliminar relaciones hacia scrap y tiempo muerto.
        $this->getProductionModelTable()->deleteCompleteProductionModel((int)$prod_model_id);

        return new JsonModel(
            array(
                "result" => 1
            )
        );
    }

    // setNoProductionDt by Ajax
    public function setNoProductionDtAjaxAction()
    {
        $request = $this->getRequest();
        $prod_model_id = $request->getPost('prod_model_id');

        // Eliminar relaciones hacia scrap y tiempo muerto.

        $ProductionDeadTime = new ProductionDeadTime();
        $ProductionDeadTime->production_model_id = (int)$prod_model_id;
        $ProductionDeadTime->death_code_id = 64;
        $ProductionDeadTime->responsible = 'Materiales';
        $ProductionDeadTime->machine_status = 0;

        $this->getProductionDeadTimeTable()->saveProductionDeadTime($ProductionDeadTime);

        return new JsonModel(
            array(
                "result" => 1
            )
        );
    }

    public function getStandardProductionTable()
    {
        if (!$this->StandardProductionTable) {
            $sm = $this->getServiceLocator();
            $this->StandardProductionTable = $sm->get('ProductionModel\Model\StandardProductionTable');
        }
        return $this->StandardProductionTable;
    }

    public function getShiftTable()
    {
        if (!$this->ShiftTable) {
            $sm = $this->getServiceLocator();
            $this->ShiftTable = $sm->get('ProductionModel\Model\ShiftTable');
        }
        return $this->ShiftTable;
    }

    public function getProductTable()
    {
        if (!$this->ProductTable) {
            $sm = $this->getServiceLocator();
            $this->ProductTable = $sm->get('ProductionModel\Model\ProductTable');
        }
        return $this->ProductTable;
    }

    public function getProductionModelTable()
    {
        if (!$this->ProductionModel) {
            $sm = $this->getServiceLocator();
            $this->ProductionModel = $sm->get('ProductionModel\Model\ProductionModelTable');
        }
        return $this->ProductionModel;
    }

    public function getProductionDeadTimeTable()
    {
        if (!$this->ProductionDeadTimeTable) {
            $sm = $this->getServiceLocator();
            $this->ProductionDeadTimeTable = $sm->get('DeadTime\Model\ProductionDeadTimeTable');
        }
        return $this->ProductionDeadTimeTable;
    }

    public function getNoProgramTable()
    {
        if (!$this->NoProgramTable) {
            $sm = $this->getServiceLocator();
            $this->NoProgramTable = $sm->get('ProductionModel\Model\NoProgramTable');
        }
        return $this->NoProgramTable;
    }
}
