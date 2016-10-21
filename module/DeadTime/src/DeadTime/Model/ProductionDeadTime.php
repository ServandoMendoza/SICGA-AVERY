<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 6/10/14
 * Time: 11:30 PM
 */

namespace DeadTime\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ProductionDeadTime implements InputFilterAwareInterface
{
    public $id;
    public $production_model_id;
    public $death_code_id;
    public $death_code_group_id;
    public $cause;
    public $responsible;
    public $action;
    public $section;
    public $problem;
    public $machine_status;
    public $dead_time;
    public $create_date;
    public $update_date;
    public $create_by;
    public $update_by;
    public $count;
    public $death_problem_id;
    public $others_responsible;
    public $other_problem;

    public $code;
    public $name;
    public $dp_description;
    public $ds_name;
    public $product_sku;
    public $start_hour;
    public $end_hour;

    public $hour_diff;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->production_model_id = (isset($data['production_model_id'])) ? $data['production_model_id'] : null;
        $this->death_code_group_id = (isset($data['death_code_group_id'])) ? $data['death_code_group_id'] : null;
        $this->code = (isset($data['code'])) ? $data['code'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->death_code_id = (isset($data['death_code_id'])) ? $data['death_code_id'] : null;
        $this->cause = (isset($data['cause'])) ? $data['cause'] : null;
        $this->responsible = (isset($data['responsible'])) ? $data['responsible'] : null;
        $this->others_responsible = (isset($data['others_responsible'])) ? $data['others_responsible'] : null;
        $this->action = (isset($data['action'])) ? $data['action'] : null;
        $this->section = (isset($data['section'])) ? $data['section'] : null;
        $this->problem = (isset($data['problem'])) ? $data['problem'] : null;
        $this->machine_status = (isset($data['machine_status'])) ? $data['machine_status'] : null;
        $this->dead_time  = (isset($data['dead_time'])) ? $data['dead_time'] : null;
        $this->create_date = (isset($data['create_date'])) ? $data['create_date'] : null;
        $this->update_date = (isset($data['update_date'])) ? $data['update_date'] : null;
        $this->create_by = (isset($data['create_by'])) ? $data['create_by'] : null;
        $this->update_by = (isset($data['update_by'])) ? $data['update_by'] : null;
        $this->count = (isset($data['count'])) ? $data['count'] : null;
        $this->hour_diff = (isset($data['hour_diff'])) ? $data['hour_diff'] : null;
        $this->death_problem_id = (isset($data['death_problem_id'])) ? $data['death_problem_id'] : null;
        $this->ds_name = (isset($data['ds_name'])) ? $data['ds_name'] : null;
        $this->dp_description = (isset($data['dp_description'])) ? $data['dp_description'] : null;
        $this->product_sku = (isset($data['product_sku'])) ? $data['product_sku'] : null;
        $this->other_problem = (isset($data['other_problem'])) ? $data['other_problem'] : null;
        $this->start_hour = (isset($data['start_hour'])) ? $data['start_hour'] : null;
        $this->end_hour = (isset($data['end_hour'])) ? $data['end_hour'] : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'problem',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'responsible',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'death_problem_id',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'death_section_id',
                'required' => false,
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
} 