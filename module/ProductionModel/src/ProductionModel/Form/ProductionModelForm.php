<?php
namespace ProductionModel\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class ProductionModelForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('production-model');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'is_replace',
            'attributes' => array(
                'type'  => 'hidden',
                'id' => 'is_replace'
            ),
        ));

        $this->add(array(
            'name' => 'to_replace_sku',
            'attributes' => array(
                'type'  => 'hidden',
                'id' => 'to_replace_sku'
            ),
        ));

        $this->add(array(
            'name' => 'altered_cycles_sp',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'start_hour',
            'attributes' => array(
                'type'  => 'hidden',
                'id' => 'start_hour',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'end_hour',
            'attributes' => array(
                'type'  => 'hidden',
                'id' => 'end_hour',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'shift_now',
            'attributes' => array(
                'type'  => 'hidden',
                'id' => 'shift_now',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'product_sku',
            'attributes' => array(
                'type'  => 'hidden',
                'id' => 'product_sku',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'sku_prefix',
            'attributes' => array(
                'id' => 'sku_prefix',
                'class' => 'form-control '
            ),
            'options' => array(
                'value_options' => array(),
            )
        ));

        $this->add(array(
            'name' => 'sku_right',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'sku_right',
                'class' => 'form-control ',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'shift_id',
            'attributes' => array(
                'id' => 'shift_id',
                'class' => 'form-control ',
                'readonly' => 'readonly'
            ),
            'options' => array(
                'value_options' => array(
                    '1' => 'Matutino',
                    '2' => 'Vespertino',
                    '3' => 'Nocturno',
                ),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'horaProduccion',
            'attributes' => array(
                'id' => 'horaProduccion',
                'class' => 'form-control '
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '7:00-8:00' => '7:00-8:00',
                    '8:00-9:00' => '8:00-9:00',
                    '9:00-10:00' => '9:00-10:00',
                ),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'program_time',
            'attributes' => array(
                'id' => 'program_time',
                'class' => 'form-control '
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '5' => '5',
                    '10' => '10',
                    '15' => '15',
                    '20' => '20',
                    '25' => '25',
                    '30' => '30',
                    '35' => '35',
                    '40' => '40',
                    '45' => '45',
                    '50' => '50',
                    '55' => '55',
                    '60' => '60',
                ),
            )
        ));

        $this->add(array(
            'name' => 'sku_size',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'sku_size',
                'class' => 'form-control ',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'actual_production',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'actual_production',
                'class' => 'form-control '
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'std_production',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'std_production',
                'class' => 'form-control',
                'readonly' => 'readonly'

            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'machine_runtime',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'machine_runtime',
                'class' => 'form-control',
                'readonly' => 'readonly'
            ),
            'options' => array(
            ),
        ));

        // Multiple Production Model Fields

        $this->add(array(
            'name' => 'guardarBtn',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Guardar',
                'id' => 'guardarBtn',
                'class' => 'btn btn-lg btn-block btn-primary'
            ),
        ));
    }
}