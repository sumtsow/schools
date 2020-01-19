<?php
namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Program implements InputFilterAwareInterface
{
	public $id;
	public $title;
	public $period;
	public $year;
    public $id_level;
	public $id_specialty; 
	public $id_form;          
	public $id_school;
	public $min_rate; 
	public $ave_rate;          
	public $max_rate;	
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id           = (isset($data['id']))           ? $data['id']           : null;
		$this->title        = (isset($data['title']))        ? $data['title']        : null;
		$this->period       = (isset($data['period']))       ? $data['period']       : 0;
		$this->year         = (isset($data['year']))         ? $data['year']         : 0;
        $this->id_level     = (isset($data['id_level']))     ? $data['id_level']     : null;
        $this->id_specialty = (isset($data['id_specialty'])) ? $data['id_specialty'] : null;
        $this->id_form      = (isset($data['id_form']))      ? $data['id_form']      : null;
        $this->id_school    = (isset($data['id_school']))    ? $data['id_school']    : null;
        $this->min_rate     = (isset($data['min_rate']))     ? $data['min_rate']     : 0;
        $this->ave_rate     = (isset($data['ave_rate']))     ? $data['ave_rate']     : 0;
        $this->max_rate     = (isset($data['max_rate']))     ? $data['max_rate']     : 0;		
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
			
            $inputFilter->add([
                'name'     => 'title',
                'required' => true,
                'filters'  => [
                    0 => ['name' => 'StripTags'],
                    1 => ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 4,
                            'max'      => 256,
                        ],
                    ],
                ],
            ]);
			
            $inputFilter->add([
                'name'     => 'period',
                'required' => false,
                'filters'  => [
                    0 => ['name' => 'StripTags'],
                    1 => ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 32,
                        ],
                    ],
                ],				
            ]);
			
            $inputFilter->add([
                'name'     => 'year',
                'required' => false,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
                'validators' => [
                    [
                        'name'    => 'Zend\Validator\Date',
                        'options' => ['format' => 'Y'],
                    ],
                    [
                        'name'    => 'Zend\Validator\GreaterThan',
                        'options' => ['min' => 2000],
                    ],					
                ],				
            ]);
			
			$inputFilter->add([
                'name'     => 'subject',
                'required' => false,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
            ]);
			
			$inputFilter->add([
                'name'     => 'id_level',
                'required' => true,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
            ]);
			
            $inputFilter->add([
                'name'     => 'id_specialty',
                'required' => true,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
            ]);
			
            $inputFilter->add([
                'name'     => 'id_form',
                'required' => true,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
            ]);
			
            $inputFilter->add([
                'name'     => 'id_school',
                'required' => true,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
            ]);
			
            $inputFilter->add([
                'name'     => 'min_rate',
                'required' => false,
				/*'validators' => [
                    [
                        'name'    => 'Zend\I18n\Validator\IsFloat',
                    ],
				],*/
            ]);
						
            $inputFilter->add([
                'name'     => 'ave_rate',
                'required' => false,
				/*'validators' => [
                    [
                        'name'    => 'Zend\I18n\Validator\IsFloat',
                    ],
				],*/
            ]);
						
            $inputFilter->add([
                'name'     => 'max_rate',
                'required' => false,
				/*/'validators' => [
                    [
                        'name'    => 'Zend\I18n\Validator\IsFloat',
                    ],
				],*/
            ]);
			
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
