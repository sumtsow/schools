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
            'name' => 'id_row',
            'attributes' => array(
				'id' => 'id_row',	
                'type'  => 'hidden',
            ),
        ));
		
		$this->add(array(
            'name' => 'id_program',
            'attributes' => array(
				'id' => 'id_program',
                'type'  => 'hidden',
            ),
        ));
		
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_subject',
            'attributes' => array(
				'id' => 'id_subject',
            ),			
			'options' => [
				'disable_inarray_validator' => true,
			]
        ));

        $this->add(array(
            'name' => 'title',
            'attributes' => array(
				'id' => 'title',
                'type'  => 'text',
            ),
        ));

        $this->add(array(
			'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'required',
			'attributes' => array(
				'id' => 'required',
            ),
			'options' => [
				'use_hidden_element' => true,
				'checked_value' => 'on',
				'unchecked_value' => null,
				'disable_inarray_validator' => true,
			],
        ));

        $this->add(array(
            'name' => 'coefficient',
            'attributes' => array(
                'id'  => 'coefficient',
				'type'  => 'text',
            ),
            'options' => [
				'disable_inarray_validator' => true,
			]
        ));
		
        $this->add(array(
            'name' => 'rating',
            'attributes' => array(
				'id'  => 'rating',
                'type'  => 'number',
				'min'  => 100,
				'max'  => 200,
            ),
            'options' => [
				'disable_inarray_validator' => true,
			]
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
                'id' => 'subjectFormSubmit',
            ),
        ));
    }
}
