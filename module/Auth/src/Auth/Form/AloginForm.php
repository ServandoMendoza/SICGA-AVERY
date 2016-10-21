<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 6/29/14
 * Time: 9:35 PM
 */

namespace Auth\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class AloginForm extends Form{
    public function __construct($name = null)
    {
        parent::__construct('alogin');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'token',
            'attributes' => array(
                'type'  => 'password',
                'class' => 'form-control'
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'class' => 'btn btn-block btn-success',
                'value' => 'Acceso Rápido',
                'id' => 'submitBtn',
            ),
        ));
    }
} 