<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 6/10/14
 * Time: 8:05 PM
 */

namespace Products\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Products implements InputFilterAwareInterface{
 
    public $sku;
    public $name;
    public $description;
    public $size;
    public $create_date;
    public $update_date;
    public $create_by;
    public $update_by;
    protected $inputFilter;
    private $sm;

    public function __construct($sm = null){
        $this->sm = $sm;
    }

    public function exchangeArray($data)
    {
        $this->sku     = (isset($data['sku'])) ? $data['sku'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->size  = (isset($data['size'])) ? $data['size'] : null;
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
                    'name' => 'sku',
                    'required' => true,
                    'filters' => array(
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
                        array(
                            'name' => 'Zend\Validator\Db\NoRecordExists',
                            'options' => array(
                                'table' => 'SICGA_Product',
                                'field' => 'sku',
                                'adapter' => $this->sm->get('Zend\Db\Adapter\Adapter'),
                            ),
                        ),
                    ),
                )));
            }
            else{
                $inputFilter->add($factory->createInput(array(
                    'name' => 'sku',
                    'required' => true,
                    'filters' => array(
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
            }

             $inputFilter->add($factory->createInput(array(
                'name'     => 'name',
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
                'name'     => 'size',
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

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}