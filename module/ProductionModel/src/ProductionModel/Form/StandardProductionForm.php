<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 9/29/14
 * Time: 3:51 PM
 */

namespace ProductionModel\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class StandardProductionForm extends Form
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
            'type' => 'Zend\Form\Element\Select',
            'name' => 'machine_id',
            'attributes' => array(
                'id' => 'machine_id',
                'class' => 'form-control',
                'readonly' => 'readonly'
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '1' => 'Windmann 10',
                ),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'size',
            'attributes' => array(
                'id' => 'size',
                'class' => 'form-control',
                'readonly' => 'readonly'
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '.5' => '.5',
                ),
            )
        ));

        $this->add(array(
            'name' => 'cycles_per_minute',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'cycles_per_minute',
                'class' => 'form-control ',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'products_per_hour',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'products_per_hour',
                'class' => 'form-control ',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'machine_runtime',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'machine_runtime',
                'class' => 'form-control ',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'crew_size',
            'attributes' => array(
                'id' => 'crew_size',
                'class' => 'form-control',
                'readonly' => 'readonly'
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ),
            )
        ));

        $this->add(array(
            'name' => 'labor_runtime',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'labor_runtime',
                'class' => 'form-control ',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'indirect_crew',
            'attributes' => array(
                'id' => 'indirect_crew',
                'class' => 'form-control',
                'readonly' => 'readonly'
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '1' => '1',
                ),
            )
        ));

        $this->add(array(
            'name' => 'saveBtn',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Guardar',
                'id' => 'saveBtn',
                'class' => 'btn btn-lg btn-block btn-primary'
            ),
        ));

    }

} 