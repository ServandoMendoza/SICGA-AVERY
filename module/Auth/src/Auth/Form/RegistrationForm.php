<?php
namespace Auth\Form;

use Zend\Form\Form;

class RegistrationForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('registration');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'usr_name',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'usr_full_name',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\File',
            'name' => 'image_file',
            'options' => array(
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'raw_password',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'photo',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'usr_password_salt',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'usr_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'bypass_token',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'usr_email',
            'attributes' => array(
                'type'  => 'email',
                'class' => 'form-control',
            ),
            'options' => array(
            ),
        ));	
		
        $this->add(array(
            'name' => 'usr_password',
            'attributes' => array(
                'type'  => 'password',
                'class' => 'form-control',
            ),
            'options' => array(
            ),
        ));
		
        $this->add(array(
            'name' => 'usr_password_confirm',
            'attributes' => array(
                'type'  => 'password',
                'class' => 'form-control',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'usrl_id',
            'attributes' => array(
                'id' => 'usrl_id',
                'class' => 'form-control',
                'readonly' => 'readonly'
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '1' => 'Operador',
                    '2' => 'Scrap',
                    '3' => 'Tecnico',
                    '5' => 'Gerente',
                    '6' => 'Dispatch',
                    '7' => 'Coordinador',
                    '4' => 'Admin',
                ),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_shift',
            'attributes' => array(
                'id' => 'id_shift',
                'class' => 'form-control',
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '1' => '1er Turno',
                    '2' => '2ndo Turno',
                    '3' => '3er Turno',
                ),
            )
        ));

//		$this->add(array(
//			'type' => 'Zend\Form\Element\Captcha',
//			'name' => 'captcha',
//			'options' => array(
//				'label' => 'Please verify you are human',
//				'captcha' => new \Zend\Captcha\Figlet(),
//			),
//		));
		
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => 'btn btn-lg btn-block btn-primary'
            ),
        )); 
    }
}