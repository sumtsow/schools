<?php

namespace School\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('user');
        $this->add(array(
            'name' => 'login',
            'attributes' => array(
                'type'  => 'text',                
            ),
            'options' => array(
                'label' => 'Логин',
            ),            
        ));
        $this->add(array(
            'name' => 'passwd',
            'attributes' => array(
                'type'  => 'password',
            ),
            'options' => array(
                'label' => 'Пароль',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'id' => 'submitbutton',
            ),
        ));
    }
}
