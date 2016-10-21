<?php
namespace ScrapCode\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class ScrapCodeForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('ScrapCode');

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'id',
            ),
        ));
        

        $this->add(array(
            'name' => 'code',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'code',
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
            'type' => 'Zend\Form\Element\Select',
            'name' => 'machine_id',
            'attributes' => array(
                'id' => 'machine_id',
                'class' => 'form-control '
            ),
            'options' => array(
                'value_options' => array(),
            )
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