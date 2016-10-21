<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 6/10/14
 * Time: 8:05 PM
 */

namespace ScrapCode\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ScrapCode implements InputFilterAwareInterface{
    public $id;
    public $code;
    public $description;
    public $machine_id;
    public $create_date;
    public $update_date;
    public $create_by;
    public $update_by;
    private $inputFilter;
    private $sm;

    public function __construct($sm = null){
        $this->sm = $sm;
    }

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->code = (isset($data['code'])) ? $data['code'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->machine_id  = (isset($data['machine_id'])) ? $data['machine_id'] : null;
        $this->create_date = (isset($data['create_date'])) ? $data['create_date'] : null;
        $this->update_date = (isset($data['update_date'])) ? $data['update_date'] : null;
        $this->create_by = (isset($data['create_by'])) ? $data['create_by'] : null;
        $this->update_by = (isset($data['update_by'])) ? $data['update_by'] : null;
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

            if($this->sm) {
                $inputFilter->add($factory->createInput(array(
                    'name' => 'code',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 1,
                                'max' => 100,
                            ),
                        ),
                        array(
                            'name' => 'Zend\Validator\Db\NoRecordExists',
                            'options' => array(
                                'table' => 'SICGA_Scrap_Code',
                                'field' => 'code',
                                'adapter' => $this->sm->get('Zend\Db\Adapter\Adapter'),
                            ),
                        ),
                    ),
                )));
            }
            else{
                $inputFilter->add($factory->createInput(array(
                    'name' => 'code',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 1,
                                'max' => 100,
                            ),
                        ),
                    ),
                )));
            }


             $inputFilter->add($factory->createInput(array(
                'name'     => 'description',
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
                'name'     => 'machine_id',
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

        return $this->inputFilter;
    }
}