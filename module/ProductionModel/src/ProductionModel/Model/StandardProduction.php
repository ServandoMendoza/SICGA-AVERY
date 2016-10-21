<?php
namespace ProductionModel\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class StandardProduction implements InputFilterAwareInterface
{
    public $id;
    public $machine_id;
    public $size;
    public $cycles_per_minute;
    public $products_per_hour;
    public $machine_runtime;
    public $crew_size;
    public $labor_runtime;
    public $indirect_crew;
    public $create_date;
    public $update_date;
    public $create_by;
    public $update_by;

    private $inputFilter;
    
    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->machine_id     = (isset($data['machine_id'])) ? $data['machine_id'] : null;
        $this->size = (isset($data['size'])) ? $data['size'] : null;
        $this->cycles_per_minute  = (isset($data['cycles_per_minute'])) ? $data['cycles_per_minute'] : null;
        $this->products_per_hour  = (isset($data['products_per_hour'])) ? $data['products_per_hour'] : null;
        $this->machine_runtime = (isset($data['machine_runtime'])) ? $data['machine_runtime'] : null;
        $this->crew_size = (isset($data['crew_size'])) ? $data['crew_size'] : null;
        $this->labor_runtime = (isset($data['labor_runtime'])) ? $data['labor_runtime'] : null;
        $this->indirect_crew = (isset($data['indirect_crew'])) ? $data['indirect_crew'] : null;
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

            $inputFilter->add($factory->createInput(array(
                'name'     => 'cycles_per_minute',
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

             $inputFilter->add($factory->createInput(array(
                 'name'     => 'products_per_hour',
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

            $inputFilter->add($factory->createInput(array(
                'name'     => 'machine_runtime',
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
