<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 6/10/14
 * Time: 8:05 PM
 */

namespace Scrap\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Scrap implements InputFilterAwareInterface{
    public $id;
    public $production_model_id;
    public $code;
    public $description;
    public $scrap_code_id;
    public $quantity;
    public $percentage;
    public $create_date;
    public $update_date;
    public $create_by;
    public $update_by;
    public $product_sku;
    public $start_hour;
    public $end_hour;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->code = (isset($data['code'])) ? $data['code'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->production_model_id = (isset($data['production_model_id'])) ? $data['production_model_id'] : null;
        $this->scrap_code_id = (isset($data['scrap_code_id'])) ? $data['scrap_code_id'] : null;
        $this->quantity  = (isset($data['quantity'])) ? $data['quantity'] : null;
        $this->percentage  = (isset($data['percentage'])) ? $data['percentage'] : null;
        $this->create_date = (isset($data['create_date'])) ? $data['create_date'] : null;
        $this->update_date = (isset($data['update_date'])) ? $data['update_date'] : null;
        $this->product_sku = (isset($data['product_sku'])) ? $data['product_sku'] : null;
        $this->start_hour = (isset($data['start_hour'])) ? $data['start_hour'] : null;
        $this->end_hour = (isset($data['end_hour'])) ? $data['end_hour'] : null;
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

            $inputFilter->add($factory->createInput(array(
                'name'     => 'scrap_code_id',
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
                'name'     => 'quantity',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^-?(?:\d+|\d*\.\d+)$/',
                            'messages' => array(
                                \Zend\Validator\Regex::NOT_MATCH => "Solo numeros",
                                \Zend\Validator\Regex::ERROROUS => "Internal Error",
                                \Zend\Validator\Regex::INVALID => "Entrada no válida",
                            ),
                        ),
                    ),
                ),
            )));

//            $inputFilter->add($factory->createInput(array(
//                'name'     => 'percentage',
//                'required' => true,
//                'filters'  => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name' => 'Regex',
//                        'options' => array(
//                            'pattern' => '/^(?:100|\d{1,2})(?:\.\d{1,2})?$/',
//                            'messages' => array(
//                                \Zend\Validator\Regex::NOT_MATCH => "Numero entre: (1-100)",
//                                \Zend\Validator\Regex::ERROROUS => "Internal Error",
//                                \Zend\Validator\Regex::INVALID => "Entrada no válida",
//                            ),
//                        ),
//                    ),
//                ),
//            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}