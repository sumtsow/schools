<?php

namespace Application\Form;

use Zend\Form\Form;

class AreaForm extends Form
{
    public function __construct($areas)
    {
        parent::__construct('area');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'area',
            'type'  => 'Zend\Form\Element\Select',
            'options' => array(
                     'label' => 'Select Area',
                     'onchange' => 'this.form.submit();',
                     'empty_option' => 'All',
                     'value_options' => $areas,
                     'selected' => 'All',
                 ),
            )
        );
    }
}