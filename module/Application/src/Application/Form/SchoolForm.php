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
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Название',
            ),
        ));
        
        $this->add(array(
            'name' => 'shortname',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Номер',
            ),
        ));
        
        $this->add(array(
            'name' => 'address',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Адрес',
            ),
        ));
        
        $this->add(array(
            'name' => 'phone',
            'attributes' => array(
                'type'  => 'tel',
            ),
            'options' => array(
                'label' => 'Телефон',
            ),
        ));
        
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'email',
            ),
            'options' => array(
                'label' => 'e-mail',
            ),
        ));
        
        $this->add(array(
            'name' => 'http',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'http',
            ),
        ));
        
        $this->add(array(
            'name' => 'info',
            'attributes' => array(
                'type'  => 'textarea',
            ),
            'options' => array(
                'label' => 'Информация',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'area',
            'id' => 'area',
            'options' => array(
                'label' => 'Район',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'high',
            'class' => 'high',
             'options' => array(
                     'label' => 'ВУЗ',
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
                'label' => 'Карта',
            ),
        ));
        
        $this->add(array(
            'name' => 'logo',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Логотип URL',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'visible',
            'class' => 'high',
             'options' => array(
                     'label' => 'Показывать',
                     'checked_value' => '1',
                     'unchecked_value' => '0'
             ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ),
        ));
    }
}
