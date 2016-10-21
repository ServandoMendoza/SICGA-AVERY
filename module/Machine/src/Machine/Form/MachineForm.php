<?php
namespace Machine\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class MachineForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('machine');

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'id',
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
            'name' => 'model',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'model',
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