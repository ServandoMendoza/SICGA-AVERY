<?php
namespace ProductionModel\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;  
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ProductionModel implements InputFilterAwareInterface
{
    public $id;
    public $machine_id;
    public $shift_id;
    public $product_sku;
    public $production_plan;
    public $start_hour;
    public $end_hour;
    public $program_time;
    public $actual_crew_size;
    public $actual_production;
    public $std_production;
    public $yield_percentage;
    public $sku_size;
    public $create_date;
    public $update_date;
    public $create_by;
    public $update_by;
    public $count;
    public $machine_runtime;
    public $alter_product_size;
    public $alter_product_size_reason;
    public $is_replace;
    public $is_replace_parent;
    public $replaced_prod_id;

    private $inputFilter;

    //From ProdModel List
    public $sku;
    public $name;
    public $number;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->machine_id     = (isset($data['machine_id'])) ? $data['machine_id'] : null;
        $this->shift_id     = (isset($data['shift_id'])) ? $data['shift_id'] : null;
        $this->product_sku = (isset($data['product_sku'])) ? $data['product_sku'] : null;
        $this->production_plan  = (isset($data['production_plan'])) ? $data['production_plan'] : null;
        $this->start_hour  = (isset($data['start_hour'])) ? $data['start_hour'] : null;
        $this->end_hour = (isset($data['end_hour'])) ? $data['end_hour'] : null;
        $this->program_time = (isset($data['program_time'])) ? $data['program_time'] : null;
        $this->actual_crew_size = (isset($data['actual_crew_size'])) ? $data['actual_crew_size'] : null;
        $this->actual_production = (isset($data['actual_production'])) ? $data['actual_production'] : null;  
        $this->std_production = (isset($data['std_production'])) ? $data['std_production'] : null;
        $this->yield_percentage = (isset($data['yield_percentage'])) ? $data['yield_percentage'] : null;
        $this->sku_size = (isset($data['sku_size'])) ? $data['sku_size'] : null;
        $this->product_sku = (isset($data['product_sku'])) ? $data['product_sku'] : null;
        $this->create_date = (isset($data['create_date'])) ? $data['create_date'] : null;
        $this->update_date = (isset($data['update_date'])) ? $data['update_date'] : null;
        $this->create_by = (isset($data['create_by'])) ? $data['create_by'] : null;
        $this->update_by = (isset($data['update_by'])) ? $data['update_by'] : null;
        $this->count     = (isset($data['count'])) ? $data['count'] : null;
        $this->machine_runtime     = (isset($data['machine_runtime'])) ? $data['machine_runtime'] : null;
        $this->alter_product_size     = (isset($data['alter_product_size'])) ? $data['alter_product_size'] : null;
        $this->alter_product_size_reason     = (isset($data['alter_product_size_reason'])) ? $data['alter_product_size_reason'] : null;
        $this->is_replace     = (isset($data['is_replace'])) ? $data['is_replace'] : null;
        $this->is_replace_parent     = (isset($data['is_replace_parent'])) ? $data['is_replace_parent'] : null;
        $this->replaced_prod_id     = (isset($data['replaced_prod_id'])) ? $data['replaced_prod_id'] : null;

        //From ProdModel List
        $this->sku     = (isset($data['sku'])) ? $data['sku'] : null;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->number     = (isset($data['number'])) ? $data['number'] : null;

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
                'name'     => 'actual_production',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function getInputReplaceFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'sku_size',
                'required' => false,
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
