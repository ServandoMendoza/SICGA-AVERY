<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 11/30/14
 * Time: 5:20 PM
 */

namespace DeadTime\Controller;

use DeadTime\Form\OpenOrderForm;
use DeadTime\Model\OpenOrder;
use DeadTime\Model\Requisition;
use DeadTime\Model\TechWork;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Custom\Utilities;

class OpenOrderController extends AbstractActionController{

    private $RequisitionTable;
    private $OpenOrderTable;
    private $TechWorkTable;

    public function addAction()
    {
        $req_number = (int) $this->params()->fromRoute('id', 0);
        if (!$req_number && !is_numeric($req_number)) {
            return $this->redirect()->toRoute('Requisition', array(
                'action' => 'pendings'
            ));
        }

        $RequisitionDM = $this->getRequisitionTable()->getRequisitionDetails($req_number);

        $request = $this->getRequest();

        $form = new OpenOrderForm();

        if ($request->isPost()) {
            $OpenOrder = new OpenOrder();

            $form->setInputFilter($OpenOrder->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $OpenOrder->exchangeArray($form->getData());

                $id_open_order = $this->getOpenOrderTable()->saveOpenOrder($OpenOrder);

                // Si No tiene plan, generamos una orden de trabajo y cerramos la requisicion
                if($OpenOrder->next_shift_plan)
                {
                    if($RequisitionDM->id_tech)
                    {
                        $TechWork = new TechWork();
                        $TechWork->type = "Orden Abierta";
                        $TechWork->id_area = $RequisitionDM->id_area;
                        $TechWork->id_machine = $RequisitionDM->id_machine;
                        $TechWork->id_shift = $RequisitionDM->id_shift;
                        $TechWork->id_tech = $RequisitionDM->id_tech;

                        $id_work = $this->getTechWorkTable()->saveTechWork($TechWork);

                        $Requisition = new Requisition();
                        $Requisition->id_open_order = $id_open_order;
                        $Requisition->number = $RequisitionDM->number;
                        $Requisition->fix_time = date('Y-m-d H:i:s');
                        $Requisition->comments = "Orden Abierta - Trabajo: $id_work";
                        $Requisition->generated_work = 1;
                        $this->getRequisitionTable()->free($Requisition);

                        return $this->redirect()->toRoute('Work', array(
                        ));
                    }
                    else{
                        $this->flashMessenger()->addMessage('No se puede generar trabajo, ya que la requisición
                        no tiene técnico asignado.');
                    }

                }
                else
                {
                   // var_dump($id_open_order);exit;

                    $Requisition = new Requisition();
                    $new_shift = $RequisitionDM->id_shift;
                    $Requisition->number = $RequisitionDM->number;
                    $Requisition->id_shift = (($new_shift + 1 ) > 3) ? 1 : $new_shift + 1;
                    $Requisition->id_open_order = $id_open_order;
                    $Requisition->fix_time = date('Y-m-d H:i:s');

                    $this->getRequisitionTable()->changeShift($Requisition);

                    return $this->redirect()->toRoute('Requisition', array(
                        'action' => 'pendings'
                    ));
                }
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'req_number' => $req_number,
            'req_data' => $RequisitionDM,
            'shift_name' => Utilities::getShiftName($RequisitionDM->id_shift),
            'flashMessages' => $this->flashMessenger()->getMessages()
        ));
    }

    public function getRequisitionTable()
    {
        if (!$this->RequisitionTable) {
            $sm = $this->getServiceLocator();
            $this->RequisitionTable = $sm->get('DeadTime\Model\RequisitionTable');
        }
        return $this->RequisitionTable;
    }

    public function getOpenOrderTable()
    {
        if (!$this->OpenOrderTable) {
            $sm = $this->getServiceLocator();
            $this->OpenOrderTable = $sm->get('DeadTime\Model\OpenOrderTable');
        }
        return $this->OpenOrderTable;
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