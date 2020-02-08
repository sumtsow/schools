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

        $this->add([
            'name' => 'id_row',
            'attributes' => ['id' => 'id_row','type'  => 'hidden']
        ]);
		
		$this->add([
            'name' => 'id_program',
            'attributes' => ['id' => 'id_program','type'  => 'hidden'],
        ]);
		
        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_subject',
            'attributes' => ['id' => 'id_subject'],
			'options' => ['disable_inarray_validator' => true]
        ]);

        $this->add(['name' => 'title','attributes' => ['id' => 'title','type'  => 'text']]);

        $this->add([
			'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'required',
			//'attributes' => ['id' => 'required'],
			'options' => [
				'label' => 'is required',
				'checked_value' => '1',
				'unchecked_value' => '0',
				'disable_inarray_validator' => true,
			],
        ]);

        $this->add([
            'name' => 'coefficient',
            'attributes' => ['id'  => 'coefficient','type'  => 'text'],
            'options' => ['disable_inarray_validator' => true]
        ]);
		
        $this->add([
            'name' => 'rating',
            'attributes' => [
				'id'  => 'rating',
                'type'  => 'number',
				'min'  => 100,
				'max'  => 200,
            ],
            'options' => ['disable_inarray_validator' => true]
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Save',
                'id' => 'subjectFormSubmit',
            ],
        ]);
    }
}
