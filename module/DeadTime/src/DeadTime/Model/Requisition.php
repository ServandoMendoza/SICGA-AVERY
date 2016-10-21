<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 6/10/14
 * Time: 8:05 PM
 */

namespace DeadTime\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Requisition implements InputFilterAwareInterface{
    public $number;
    public $id_area;
    public $id_machine;
    public $machine_status;
    public $problem;
    public $id_shift;
    public $id_tech;
    public $cause;
    public $action;
    public $comments;
    public $assign_time;
    public $fix_time;
    public $create_date;
    public $id_dead_time;
    public $free;
    public $id_open_order;
    public $generated_work;
    public $other_problem;

    public $machine_name;
    public $area_name;
    public $responsible;
    public $tech_name;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->number = (isset($data['number'])) ? $data['number'] : null;
        $this->id_area = (isset($data['id_area'])) ? $data['id_area'] : null;
        $this->id_machine = (isset($data['id_machine'])) ? $data[id_machine] : null;
        $this->machine_status  = (isset($data['machine_status'])) ? $data['machine_status'] : null;
        $this->problem = (isset($data['problem'])) ? $data['problem'] : null;
        $this->id_shift = (isset($data['id_shift'])) ? $data['id_shift'] : null;
        $this->id_tech = (isset($data['id_tech'])) ? $data['id_tech'] : null;
        $this->cause = (isset($data['cause'])) ? $data['cause'] : null;
        $this->action = (isset($data['action'])) ? $data['action'] : null;
        $this->comments = (isset($data['comments'])) ? $data['comments'] : null;
        $this->assign_time = (isset($data['assign_time'])) ? $data['assign_time'] : null;
        $this->fix_time = (isset($data['fix_time'])) ? $data['fix_time'] : null;
        $this->create_date = (isset($data['create_date'])) ? $data['create_date'] : null;
        $this->id_dead_time = (isset($data['id_dead_time'])) ? $data['id_dead_time'] : null;
        $this->machine_name = (isset($data['machine_name'])) ? $data['machine_name'] : null;
        $this->tech_name = (isset($data['tech_name'])) ? $data['tech_name'] : null;
        $this->free = (isset($data['free'])) ? $data['free'] : null;
        $this->area_name = (isset($data['area_name'])) ? $data['area_name'] : null;
        $this->responsible = (isset($data['responsible'])) ? $data['responsible'] : null;
        $this->area_name = (isset($data['area_name'])) ? $data['area_name'] : null;
        $this->id_open_order = (isset($data['id_open_order'])) ? $data['id_open_order'] : null;
        $this->generated_work = (isset($data['generated_work'])) ? $data['generated_work'] : null;
        $this->other_problem = (isset($data['other_problem'])) ? $data['other_problem'] : null;
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
                'name'     => 'number',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'responsible',
                'required' => false,
            )));

            $this->inputFilter = $inputFilter;
        }

        $this->inputFilter = $inputFilter;

        return $this->inputFilter;
    }
}