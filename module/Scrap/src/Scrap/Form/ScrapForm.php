<?php
namespace Scrap\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class ScrapForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('scrap');

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'id',
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
            'name' => 'scrap_code_id',
            'attributes' => array(
                'id' => 'scrap_code_id',
                'class' => 'form-control',
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '1' => 'A',
                    '2' => 'B',
                ),
            )
        ));

//        $this->add(array(
//            'name' => 'scrap_description',
//            'attributes' => array(
//                'type'  => 'text',
//                'readonly' => 'readonly',
//                'id' => 'scrap_description',
//            ),
//            'options' => array(
//                'label' => 'Descripcion del codigo:',
//            ),
//        ));

        $this->add(array(
            'name' => 'quantity',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'quantity',
                'class' => 'form-control',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'percentage',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'percentage',
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