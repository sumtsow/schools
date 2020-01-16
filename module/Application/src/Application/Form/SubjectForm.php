<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class SubjectForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('subject');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
		$this->add(array(
            'name' => 'id_program',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
        $this->add(array(
            'name' => 'id_subject',
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
			//'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'required',
			'options' => [
				'use_hidden_element' => true,
				'checked_value' => 1,
				'unchecked_value' => 0,
			],
            'attributes' => array(
                'type'  => 'text',
            ),
        ));

        $this->add(array(
            'name' => 'coefficient',
            'attributes' => array(
                'type'  => 'number',
				'min'  => 0,
				'max'  => 1,
            ),
        ));
		
        $this->add(array(
            'name' => 'rating',
            'attributes' => array(
                'type'  => 'number',
				'min'  => 100,
				'max'  => 200,
            ),
        ));
    }
}
