<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class ProgramForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('program');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id_school',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_specialty',
            'id' => 'id_specialty',
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_level',
            'id' => 'id_level',
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_form',
            'id' => 'id_form',
        ));
    }
}
