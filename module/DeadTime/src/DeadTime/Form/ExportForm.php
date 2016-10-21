<?php
namespace DeadTime\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class ExportForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('export-form');

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'start_date',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'start_date',
                'class' => 'form-control',
                'readonly' => 'true'
            ),
            'options' => array(

            ),
        ));

        $this->add(array(
            'name' => 'end_date',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'end_date',
                'class' => 'form-control',
                'readonly' => 'true'
            ),
            'options' => array(

            ),
        ));

        $this->add(array(
            'name' => 'exportBtn',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Exportar',
                'id' => 'exportBtn',
                'class' => 'btn btn-success btn-block btn-lg'
            ),
        ));
    }

}