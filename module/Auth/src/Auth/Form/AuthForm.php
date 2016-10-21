<?php
namespace Auth\Form;

use Zend\Form\Form;

class AuthForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('auth');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'cprod-name',
            'attributes' => array(
                'type'  => 'hidden',
                'id'  => 'cprod-name',
            ),
        ));

        $this->add(array(
            'name' => 'usr_name',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control usuario',
                'placeholder' => 'Usuario',
            ),
        ));
        $this->add(array(
            'name' => 'usr_password',
            'attributes' => array(
                'type'  => 'password',
                'class' => 'form-control pass',
                'placeholder' => 'Contraseña'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'cprod-cbx',
            'attributes' => array(
                'id' => 'cprod-cbx',
                'class' => 'form-control',
            ),
            'options' => array(
                'value_options' => array(
                    '1' => 'WIDMANN-10',
                ),
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Iniciar Sesión',
                'id' => 'submitbutton',
                'class' => 'btn btn-success btn-lg btn-block'
            ),
        )); 
    }
}