<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 11/27/14
 * Time: 4:13 PM
 */

namespace DeadTime\Model;

class RequisitionAssignment{
    public $id;
    public $id_requisition;
    public $id_tech;
    public $acc_time;
    public $assign_time;
    public $assign_date;

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->id_requisition = (isset($data['id_requisition'])) ? $data['id_requisition'] : null;
        $this->id_tech = (isset($data['id_tech'])) ? $data['id_tech'] : null;
        $this->acc_time  = (isset($data['acc_time'])) ? $data['acc_time'] : null;
        $this->assign_time = (isset($data['assign_time'])) ? $data['assign_time'] : null;
        $this->assign_date = (isset($data['assign_date'])) ? $data['assign_date'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
} 