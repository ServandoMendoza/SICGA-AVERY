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

class TechWork implements InputFilterAwareInterface{
    public $number;
    public $type;
    public $id_area;
    public $id_machine;
    public $id_shift;
    public $id_tech;
    public $total;
    public $crono;
    public $comments;
    public $stop_date;
    public $acc_mins;
    public $create_date;
    public $update_date;
    public $stopped;
    public $free;

    public $area_name;
    public $machine_name;
    public $shift_name;
    public $usr_name;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->number = (isset($data['number'])) ? $data['number'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->id_area  = (isset($data['id_area'])) ? $data['id_area'] : null;
        $this->id_machine = (isset($data['id_machine'])) ? $data['id_machine'] : null;
        $this->id_shift = (isset($data['id_shift'])) ? $data['id_shift'] : null;
        $this->id_tech = (isset($data['id_tech'])) ? $data['id_tech'] : null;
        $this->total = (isset($data['total'])) ? $data['total'] : null;
        $this->crono = (isset($data['crono'])) ? $data['crono'] : null;
        $this->comments = (isset($data['comments'])) ? $data['comments'] : null;
        $this->create_date = (isset($data['create_date'])) ? $data['create_date'] : null;
        $this->update_date = (isset($data['update_date'])) ? $data['update_date'] : null;
        $this->area_name = (isset($data['area_name'])) ? $data['area_name'] : null;
        $this->machine_name = (isset($data['machine_name'])) ? $data['machine_name'] : null;
        $this->shift_name = (isset($data['shift_name'])) ? $data['shift_name'] : null;
        $this->usr_name = (isset($data['usr_name'])) ? $data['usr_name'] : null;
        $this->stop_date = (isset($data['stop_date'])) ? $data['stop_date'] : null;
        $this->acc_mins = (isset($data['acc_mins'])) ? $data['acc_mins'] : null;
        $this->free = (isset($data['free'])) ? $data['free'] : null;
        $this->stopped = (isset($data['stopped'])) ? $data['stopped'] : null;
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
                'name'     => 'create_date',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id_area',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id_machine',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id_tech',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));


            $inputFilter->add($factory->createInput(array(
                'name'     => 'id_shift',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        $this->inputFilter = $inputFilter;

        return $this->inputFilter;
    }
}