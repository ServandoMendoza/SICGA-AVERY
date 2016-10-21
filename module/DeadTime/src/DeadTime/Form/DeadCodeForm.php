<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 9/26/14
 * Time: 5:17 PM
 */

namespace DeadTime\Form;

use Zend\Form\Element;
use Zend\Form\Form;


class DeadCodeForm extends Form {

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('dead-code');

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'id',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'machine_id',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'machine_id'

            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                ),
            )
        ));

        $this->add(array(
            'name' => 'code',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'code',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'description',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'death_code_group_id',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'death_code_group_id'

            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '1' => 'activo',
                    '0' => 'inactivo',
                ),
            )
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