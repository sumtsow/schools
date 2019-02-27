<?php
namespace School\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class School implements InputFilterAwareInterface
{
	public $id;
	public $name;	
	public $address;
	public $phone;
	public $email;
	public $http;	
	public $info;
	public $area;
	public $high;
	public $map;
        protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id       = (isset($data['id'])) ? $data['id']     : null;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->address  = (isset($data['address'])) ? $data['address']  : null;
        $this->phone    = (isset($data['phone'])) ? $data['phone']     : null;
        $this->email    = (isset($data['email'])) ? $data['email'] : null;
        $this->http     = (isset($data['http'])) ? $data['http']  : null;
        $this->info     = (isset($data['info'])) ? $data['info']  : null;
        $this->area     = (isset($data['area'])) ? $data['area']     : null;
        $this->high     = (isset($data['high'])) ? $data['high'] : null;
        $this->map      = (isset($data['map'])) ? $data['map']  : null;		
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
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'name',
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
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'address',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 300,
                        ),
                    ),
                ),
            )));
			
	$inputFilter->add($factory->createInput(array(
                'name'     => 'phone',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
			
			$inputFilter->add($factory->createInput(array(
                'name'     => 'email',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
			
			$inputFilter->add($factory->createInput(array(
                'name'     => 'http',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 50,
                        ),
                    ),
                ),
            )));
			
			$inputFilter->add($factory->createInput(array(
                'name'     => 'info',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 300,
                        ),
                    ),
                ),
            )));
			
			$inputFilter->add($factory->createInput(array(
                'name'     => 'area',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 50,
                        ),
                    ),
                ),
            )));
			
			$inputFilter->add($factory->createInput(array(
                'name'     => 'high',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 1,
                        ),
                    ),
                ),
            )));
			
			$inputFilter->add($factory->createInput(array(
                'name'     => 'map',
                'required' => false,
                'filters'  => array(
                    //array('name' => 'StripTags'),
                    //array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 1000,
                        ),
                    ),
                ),
            )));
			
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}