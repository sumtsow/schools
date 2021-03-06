<?php
namespace Application\Form;

use Zend\Form\Form;

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
                'label' => 'User',
            ),
        ));
        $this->add(array(
            'name' => 'time',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Date & Time',
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
                'value' => 'Save',
                'id' => 'submitbutton',
            ),
        ));
        $this->add(array(
            'name' => 'delComment',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Delete',
                'id' => 'delComment',
                'title' => 'Delete',
            ),
        ));
     }
}