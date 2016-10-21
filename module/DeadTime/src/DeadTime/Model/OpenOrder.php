<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 11/30/14
 * Time: 4:23 PM
 */

namespace DeadTime\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class OpenOrder implements InputFilterAwareInterface{
    public $id;
    public $details;
    public $next_shift_plan;
    public $recommendations;
    public $problem_solution;
    public $assign_date;
    public $create_date;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->details = (isset($data['details'])) ? $data['details'] : null;
        $this->next_shift_plan = (isset($data['next_shift_plan'])) ? $data['next_shift_plan'] : null;
        $this->recommendations  = (isset($data['recommendations'])) ? $data['recommendations'] : null;
        $this->problem_solution = (isset($data['problem_solution'])) ? $data['problem_solution'] : null;
        $this->create_date = (isset($data['create_date'])) ? $data['create_date'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {

        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'details',
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

            $this->inputFilter = $inputFilter;
        }

        $this->inputFilter = $inputFilter;

        return $this->inputFilter;
    }
} 