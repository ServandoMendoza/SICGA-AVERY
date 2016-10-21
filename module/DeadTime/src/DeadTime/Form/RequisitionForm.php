<?php
namespace DeadTime\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class RequisitionForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('requisition-form');

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'modify',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'modify',
                'value' => 'assign',
            ),
        ));

        $this->add(array(
            'name' => 'id_dead_time',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'id_dead_time',
            ),
        ));

        $this->add(array(
            'name' => 'generated_work',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'generated_work',
            ),
        ));

        $this->add(array(
            'name' => 'id_open_order',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'id_open_order',
            ),
        ));

        $this->add(array(
            'name' => 'number',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'number',
                'class' => 'form-control',
                'readonly' => 'true'
            ),
            'options' => array(

            ),
        ));

        $this->add(array(
            'name' => 'tech1',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'tech1',
                'class' => 'form-control',
                'readonly' => 'true'
            ),
            'options' => array(

            ),
        ));

        $this->add(array(
            'name' => 'tech2',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'tech2',
                'class' => 'form-control',
                'readonly' => 'true'
            ),
            'options' => array(

            ),
        ));

        $this->add(array(
            'name' => 'tech3',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'tech3',
                'class' => 'form-control',
                'readonly' => 'true'
            ),
            'options' => array(

            ),
        ));

        $this->add(array(
            'name' => 'create_date',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'create_date',
                'class' => 'form-control',
                'readonly' => 'true'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_area',
            'attributes' => array(
                'id' => 'id_area',
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
            'name' => 'id_machine',
            'attributes' => array(
                'id' => 'id_machine',
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
            'name' => 'machine_status',
            'attributes' => array(
                'id' => 'machine_status',
                'class' => 'form-control',
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
            'name' => 'problem',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'problem',
                'class' => 'form-control',
                'readonly' => 'true'
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
                    '' => 'Sin Asignar',
                    'Operaciones' => 'Operaciones',
                    'Materiales' => 'Materiales',
                    'Ingenieria' => 'Ingenieria',
                    'Otros' => 'Otros',
                ),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_shift',
            'attributes' => array(
                'id' => 'id_shift',
                'class' => 'form-control',
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '1' => '1er Turno',
                    '2' => '2ndo Turno',
                    '3' => '3er Turno',
                ),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_tech',
            'attributes' => array(
                'id' => 'id_tech',
                'class' => 'form-control'
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                ),
            )
        ));

        $this->add(array(
            'name' => 'assign_time',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'assign_time',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'free',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'free',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'cause',
            'attributes' => array(
                'type'  => 'text',
                'readonly' => 'true',
                'id' => 'cause',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'action',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'action',
                'readonly' => 'true',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'comments',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'comments',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'fix_time',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'fix_time',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'wrBtn',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Trabajos/Descansos',
                'id' => 'wrBtn',
                'class' => 'btn btn-primary btn-block btn-lg'
            ),
        ));

        $this->add(array(
            'name' => 'pendingsBtn',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Pendientes',
                'id' => 'pendingsBtn',
                'class' => 'btn btn-primary btn-block btn-lg'
            ),
        ));

        $this->add(array(
            'name' => 'requisitionsBtn',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Requisiciones',
                'id' => 'requisitionsBtn',
                'class' => 'btn btn-primary btn-block btn-lg'
            ),
        ));

        $this->add(array(
            'name' => 'assignTechBtn',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Asignar',
                'id' => 'assignTechBtn',
                'class' => 'btn btn-primary btn-block btn-lg'
            ),
        ));

        $this->add(array(
            'name' => 'freeReqBtn',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Cerrar',
                'id' => 'freeReqBtn',
                'class' => 'btn btn-primary btn-block btn-lg'
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