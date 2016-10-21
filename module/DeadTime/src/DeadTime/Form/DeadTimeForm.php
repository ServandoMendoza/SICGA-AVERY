<?php
namespace DeadTime\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class DeadTimeForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('dead-time');

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'id',
            ),
        ));

        $this->add(array(
            'name' => 'problem',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'problem',
            ),
        ));

        $this->add(array(
            'name' => 'production_model_id',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'production_model_id',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'dead_code_group',
            'attributes' => array(
                'id' => 'dead_code_group',
                'class' => 'form-control'
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                ),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'death_code_id',
            'attributes' => array(
                'id' => 'death_code_id',
                'class' => 'form-control'
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                ),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'death_problem_id',
            'attributes' => array(
                'id' => 'death_problem_id',
                'class' => 'form-control'
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                ),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'death_section_id',
            'attributes' => array(
                'id' => 'death_section_id',
                'class' => 'form-control'
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                ),
            )
        ));

        $this->add(array(
            'name' => 'cause',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'cause',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'responsible',
            'attributes' => array(
                'id' => 'responsible',
                'class' => 'form-control'
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    'Operaciones' => 'Operaciones',
                    'Materiales' => 'Materiales',
                    'Ingenieria' => 'Ingenieria',
                    'Otros' => 'Otros',
                ),
            )
        ));

        $this->add(array(
            'name' => 'others_responsible',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'others_responsible',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'action',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'action',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'section',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'section',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'machine_status',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'machine_status'

            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '1' => 'activo',
                    '2' => 'activo con problemas',
                    '0' => 'inactivo',
                ),
            )
        ));

        $this->add(array(
            'name' => 'other_problem',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'other_problem',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'saveBtn',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Agregar',
                'id' => 'saveBtn',
                'class' => 'btn btn-success btn-block btn-lg'
            ),
        ));
    }

}