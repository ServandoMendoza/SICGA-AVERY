<?php

namespace Products\Form;

use Zend\Form\Element;
use Zend\Form\Form;


class ProductsForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('products');

        $this->setAttribute('method', 'post');



        $this->add(array(
            'name' => 'sku',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'sku',
                'class' => 'form-control',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'name',
                'class' => 'form-control',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'description',
                'class' => 'form-control',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'size',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'size',
                'class' => 'form-control',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'saveBtn',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Agregar',
                'class' => 'btn btn-success btn-lg btn-block',
                'id' => 'saveBtn',
            ),
        ));
    }

}