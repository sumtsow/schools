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
            'name' => 'id',
            'attributes' => array(
				'id'  => 'id',
                'type'  => 'hidden',
            ),
        ));
		
        $this->add(array(
            'name' => 'id_school',
            'attributes' => array(
                'type'  => 'hidden',
				'id'  => 'id_school',
            ),
        ));
		
        $this->add(array(
            'name' => 'id_edbo',
            'attributes' => array(
                'id'  => 'id_edbo',
				'type'  => 'number',
            ),
        ));
		
        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'id'  => 'title',
				'type'  => 'text',
            ),
        ));
		
        $this->add(array(
			'type' => 'Zend\Form\Element\Select',
            'name' => 'type',
            'attributes' => array(
                'id'  => 'type',
            ),
			'options' => [
				'disable_inarray_validator' => true,
			]
        ));
		
        $this->add(array(
            'name' => 'period',
            'attributes' => array(
                'id'  => 'period',
				'type'  => 'text',
            ),
        ));

        $this->add(array(
            'name' => 'year',
            'attributes' => array(
				'id'  => 'year',
                'type'  => 'number',
				'min'  => 2000,
				'max'  => date('Y'),
            ),
			'options' => [
				'disable_inarray_validator' => true,
			]
        ));
		
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_specialty',
            'attributes' => array(
                'id'  => 'id_specialty',
            ),
			'options' => [
				'disable_inarray_validator' => true,
			]
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_level',
            'attributes' => array(
                'id'  => 'id_level',
            ),
			'options' => [
				'disable_inarray_validator' => true,
			]
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_base',
            'attributes' => array(
                'id'  => 'id_base',
            ),
			'options' => [
				'disable_inarray_validator' => true,
			]
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_form',
            'attributes' => array(
                'id'  => 'id_form',
            ),
			'options' => [
				'disable_inarray_validator' => true,
			]
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'subject',
            'attributes' => array(
                'id'  => 'subject',
            ),
			'options' => [
				'disable_inarray_validator' => true,
			]
        ));

        $this->add(array(
            'name' => 'min_rate',
            'attributes' => array(
                'type'  => 'text',
				'id'  => 'min_rate'
            ),
        ));
		
        $this->add(array(
            'name' => 'ave_rate',
            'attributes' => array(
                'type'  => 'text',
				'id'  => 'ave_rate'
            ),
        ));
		
        $this->add(array(
            'name' => 'max_rate',
            'attributes' => array(
                'type'  => 'text',
				'id'  => 'max_rate'
            ),
        ));

        $this->add(array(
			'type' => 'Zend\Form\Element\Date',
            'name' => 'learning_start',
			'options' => array(
				'label' => 'Learning start date',
				'format' => 'Y-m-d'
			),
			'attributes' => array(
				'min' => '2000-01-01',
				'max' => '2049-12-31',
			)
        ));

        $this->add(array(
			'type' => 'Zend\Form\Element\Date',
            'name' => 'learning_end',
			'options' => array(
				'label' => 'Learning end date',
				'format' => 'Y-m-d'
			),
			'attributes' => array(
				'min' => '2000-01-01',
				'max' => '2049-12-31',
			)
        ));

        $this->add(array(
			'type' => 'Zend\Form\Element\Date',
            'name' => 'entrance_start',
			'options' => array(
				'label' => 'Entrance start date',
				'format' => 'Y-m-d'
			),
			'attributes' => array(
				'min' => '2000-01-01',
				'max' => '2049-12-31',
			)
        ));

        $this->add(array(
			'type' => 'Zend\Form\Element\Date',
            'name' => 'entrance_end',
			'options' => array(
			'label' => 'Entrance end date',
			'format' => 'Y-m-d'
			),
			'attributes' => array(
			'min' => '2000-01-01',
			'max' => '2049-12-31',
			)
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
                'id' => 'programFormSubmit',
            ),
        ));
    }
}
