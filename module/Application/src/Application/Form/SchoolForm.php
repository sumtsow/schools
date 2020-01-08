<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class SchoolForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('school');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'program',
            'id' => 'program',
			'multiple' => 'multiple',
        ));
		
        $this->add(array(
            'name' => 'name_uk',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
                
        $this->add(array(
            'name' => 'name_en',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
                
        $this->add(array(
            'name' => 'name_ru',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
        
        $this->add(array(
            'name' => 'shortname',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Short Name',
            ),
        ));
        
        $this->add(array(
            'name' => 'address',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Address',
            ),
        ));
        
        $this->add(array(
            'name' => 'phone',
            'attributes' => array(
                'type'  => 'tel',
            ),
            'options' => array(
                'label' => 'Phone',
            ),
        ));
        
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'email',
            ),
            'options' => array(
                'label' => 'E-mail',
            ),
        ));
        
        $this->add(array(
            'name' => 'http',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'WWW',
            ),
        ));
        
        $this->add(array(
            'name' => 'info',
            'attributes' => array(
                'type'  => 'textarea',
            ),
            'options' => array(
                'label' => 'Information',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'area',
            'id' => 'area',
            'options' => array(
                'label' => 'Area',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'high',
            'class' => 'high',
             'options' => array(
                     'label' => 'Is an University',
                     'checked_value' => '1',
                     'unchecked_value' => '0'
             ),
        ));
        
        $this->add(array(
            'name' => 'map',
            'attributes' => array(
                'type'  => 'textarea',
            ),
            'options' => array(
                'label' => 'Map',
            ),
        ));
        
        $this->add(array(
            'name' => 'logo',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Logo URL',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'visible',
            'class' => 'high',
             'options' => array(
                     'label' => 'Visible',
                     'checked_value' => '1',
                     'unchecked_value' => '0'
             ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
                'id' => 'submitbutton',
            ),
        ));
    }
}
