<?php
namespace School\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class CommentForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('comment');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));  
        $this->add(array(
            'name' => 'id_school',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));      
        $this->add(array(
            'name' => 'author',
            'attributes' => array(
                'type'  => 'text',
                'value' => 'Гость',
            ),
            'options' => array(
                'label' => 'Пользователь',
            ),
        ));
        $this->add(array(
            'name' => 'time',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Дата и время',
            ),
        ));        
        $this->add(array(
            'name' => 'text',
            'attributes' => array(
                'type'  => 'textarea',
            ),
        ));
        $this->add(array(
            'name' => 'visible',
            'type'  => 'Zend\Form\Element\Checkbox',
            'options' => array(
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