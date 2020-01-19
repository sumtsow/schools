<?php
namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class School implements InputFilterAwareInterface
{
	public $id;
	public $id_edbo;	
	public $programs;
    public $name_uk;
	public $name_en; 
	public $name_ru;          
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
        $this->id        = (isset($data['id'])) ? $data['id'] : null;
		$this->id_edbo   = (isset($data['id_edbo'])) ? $data['id_edbo'] : null;
		$this->program   = (isset($data['program'])) ? $data['program'] : null;
        $this->name_uk   = (isset($data['name_uk'])) ? $data['name_uk'] : null;
        $this->name_en   = (isset($data['name_en'])) ? $data['name_en'] : null;
        $this->name_ru   = (isset($data['name_ru'])) ? $data['name_ru'] : null;
        $this->shortname = (isset($data['shortname'])) ? $data['shortname'] : null;
        $this->address   = (isset($data['address'])) ? $data['address'] : null;
        $this->phone     = (isset($data['phone'])) ? $data['phone'] : null;
        $this->email     = (isset($data['email'])) ? $data['email'] : null;
        $this->http      = (isset($data['http'])) ? $data['http'] : null;
        $this->info      = (isset($data['info'])) ? $data['info'] : null;
        $this->area      = (isset($data['area'])) ? $data['area'] : null;
        $this->high      = (isset($data['high'])) ? $data['high'] : null;
        $this->map       = (isset($data['map'])) ? $data['map'] : null;
        $this->logo      = (isset($data['logo'])) ? $data['logo'] : null;
        $this->visible   = (isset($data['visible'])) ? $data['visible'] : null;        
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

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id_edbo',
                'required' => false,
                'filters'  => array(
                    array('name' => 'ToInt'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'program',
                'required' => false,
                'filters'  => array(
                    array('name' => 'ToInt'),
                ),
                /*'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 256,
                        ),
                    ),
                ),*/
            )));
			
            $inputFilter->add($factory->createInput(array(
                'name'     => 'name_uk',
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
                            'max'      => 256,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'name_en',
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
                            'max'      => 256,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'name_ru',
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
                            'max'      => 256,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'shortname',
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
                            'max'      => 16,
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
                            'max'      => 256,
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
                            'max'      => 64,
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
                            'max'      => 64,
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
                            'max'      => 128,
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
                            'max'      => 2048,
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
                            'max'      => 1024,
                        ),
                    ),
                ),
            )));
			
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
