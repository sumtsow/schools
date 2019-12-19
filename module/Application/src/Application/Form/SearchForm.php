<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class SearchForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('search');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'search',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                     'label' => 'Select level',
                     //'onchange' => 'this.form.submit();',
                     //'empty_option' => 'All',
                     //'selected' => 'All',
                 ),
            )
        );
    }
}
