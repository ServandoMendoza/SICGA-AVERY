<?php
namespace DeadTime\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class TechWorkForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('tech-work');

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'number',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'number',
            ),
        ));

        $this->add(array(
            'name' => 'free',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'free',
            ),
        ));

        $this->add(array(
            'name' => 'create_date',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'create_date',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'type',
            'attributes' => array(
                'id' => 'type',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    'Mejora' => 'Mejora',
                    'Preventivo' => 'Preventivo',
                    'ESP' => 'Tarjeta ESP.',
                    'Orden Abierta' => 'Orden Abierta',
                    'Otros' => 'Otros',
                ),
            )
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
                    '1' => 'Area1',
                    '2' => 'Area2',
                    '3' => 'Area3',

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
                    'Seleccione Area...'
                ),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_shift',
            'attributes' => array(
                'id' => 'id_shift',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    '1' => '1er Turno',
                    '2' => '2do Turno',
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
                    '1' => 'Juan Perez',
                    '2' => 'Miguel Rodriguez',
                ),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'crono',
            'attributes' => array(
                'id' => 'crono',
                'class' => 'form-control'
            ),
            'options' => array(
                'value_options' => array(
                    'Corriendo' => 'Corriendo',
                    'Pausa' => 'En Pausa',
                    'Terminado' => 'Terminado',
                ),
            )
        ));


        $this->add(array(
            'name' => 'total',
            'attributes' => array(
                'type'  => 'total',
                'id' => 'responsible',
                'class' => 'form-control',
                'readonly' => 'true'
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
            'name' => 'saveBtn',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Inicio/Paro',
                'id' => 'saveBtn',
                'class' => 'btn btn-success btn-block btn-lg'
            ),
        ));

        $this->add(array(
            'name' => 'finishBtn',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Terminar',
                'id' => 'finishBtn',
                'class' => 'btn btn-warning btn-block btn-lg'
            ),
        ));
    }

}