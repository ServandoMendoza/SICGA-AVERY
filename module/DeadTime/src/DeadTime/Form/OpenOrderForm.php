<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 11/30/14
 * Time: 4:33 PM
 */

namespace DeadTime\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class OpenOrderForm extends Form {
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('open-order');

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'id',
            ),
        ));

        $this->add(array(
            'name' => 'details',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'details',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'next_shift_plan',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));

        $this->add(array(
            'name' => 'recommendations',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'recommendations',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'problem_solution',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'problem_solution',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'saveBtn',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Proceder',
                'id' => 'saveBtn',
                'class' => 'btn btn-success btn-block btn-lg'
            ),
        ));
    }
} 