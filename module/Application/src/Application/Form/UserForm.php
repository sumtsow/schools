<?php

namespace Application\Form;

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
                'label' => 'Login',
            ),            
        ));
        $this->add(array(
            'name' => 'passwd',
            'attributes' => array(
                'type'  => 'password',
            ),
            'options' => array(
                'label' => 'Password',
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
