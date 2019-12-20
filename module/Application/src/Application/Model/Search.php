<?php
namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Search implements InputFilterAwareInterface
{
    /**
     * @var int Education Level ID
     * @var array ZNO subjects 
     * @var int Education Form ID
     */
    public $level;
    public $subjects;
    public $form;
	
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id       = (isset($data['id']))       ? $data['id']       : null;
        $this->level    = (isset($data['level']))    ? $data['level']    : null;
		$this->subjects = (isset($data['subjects'])) ? $data['subjects'] : null;
		$this->form     = (isset($data['form']))     ? $data['form']    : null;
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
                'name'     => 'id',
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