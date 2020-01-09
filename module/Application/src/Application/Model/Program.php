<?php
namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Program implements InputFilterAwareInterface
{
	public $id;
	public $title;
    public $id_level;
	public $id_specialty; 
	public $id_form;          
	public $id_school;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id           = (isset($data['id']))           ? $data['id']           : null;
		$this->title        = (isset($data['title']))        ? $data['title']        : null;
        $this->id_level     = (isset($data['id_level']))     ? $data['id_level']     : null;
        $this->id_specialty = (isset($data['id_specialty'])) ? $data['id_specialty'] : null;
        $this->id_form      = (isset($data['id_form']))      ? $data['id_form']      : null;
        $this->id_school    = (isset($data['id_school']))    ? $data['id_school']    : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
	
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
			
            $inputFilter->add($factory->createInput(array(
                'name'     => 'title',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 4,
                            'max'      => 256,
                        ),
                    ),
                ),
            )));
			
            $inputFilter->add($factory->createInput(array(
                'name'     => 'id_level',
                'required' => true,
                'filters'  => array(
                    array('name' => 'ToInt'),
                ),
            )));
			
            $inputFilter->add($factory->createInput(array(
                'name'     => 'id_specialty',
                'required' => true,
                'filters'  => array(
                    array('name' => 'ToInt'),
                ),
            )));
			
            $inputFilter->add($factory->createInput(array(
                'name'     => 'id_form',
                'required' => true,
                'filters'  => array(
                    array('name' => 'ToInt'),
                ),
            )));
			
            $inputFilter->add($factory->createInput(array(
                'name'     => 'id_school',
                'required' => true,
                'filters'  => array(
                    array('name' => 'ToInt'),
                ),
            )));
			
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
