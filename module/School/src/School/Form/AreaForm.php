<?php

namespace School\Form;

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
                     'label' => 'Показать район',
                     'onchange' => 'this.form.submit();',
                     'empty_option' => 'Все',
                     'value_options' => $areas,
                     'selected' => 'Все',
                 ),
            )
        );
    }
}