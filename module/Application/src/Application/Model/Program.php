<?php
namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Program implements InputFilterAwareInterface
{
	public $id;
	public $id_edbo;
	public $title;
	public $type;
	public $period;
	public $year;
    public $id_level;
	public $id_specialty;
	public $id_form;
	public $id_school;
	public $id_base;
	public $min_rate; 
	public $ave_rate;          
	public $max_rate;
	public $learning_start;
	public $learning_end;
	public $entrance_start;
	public $entrance_end;	
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id             = (isset($data['id']))             ? $data['id']             : null;
		$this->id_edbo        = (isset($data['id_edbo']))        ? $data['id_edbo']        : null;
		$this->title          = (isset($data['title']))          ? $data['title']          : null;
		$this->type           = (isset($data['type']))           ? $data['type']           : null;
		$this->period         = (isset($data['period']))         ? $data['period']         : 0;
		$this->year           = (isset($data['year']))           ? $data['year']           : date('Y');
        $this->id_level       = (isset($data['id_level']))       ? $data['id_level']       : 2;
        $this->id_specialty   = (isset($data['id_specialty']))   ? $data['id_specialty']   : null;
        $this->id_form        = (isset($data['id_form']))        ? $data['id_form']        : 1;
        $this->id_school      = (isset($data['id_school']))      ? $data['id_school']      : null;
        $this->id_base        = (isset($data['id_base']))        ? $data['id_base']        : 2;
        $this->min_rate       = (isset($data['min_rate']))       ? $data['min_rate']       : 0;
        $this->ave_rate       = (isset($data['ave_rate']))       ? $data['ave_rate']       : 0;
        $this->max_rate       = (isset($data['max_rate']))       ? $data['max_rate']       : 0;
		$this->learning_start = (isset($data['learning_start'])) ? $data['learning_start'] : null;
		$this->learning_end   = (isset($data['learning_end']))   ? $data['learning_end']   : null;
		$this->entrance_start = (isset($data['entrance_start'])) ? $data['entrance_start'] : null;
		$this->entrance_end   = (isset($data['entrance_end']))   ? $data['entrance_end']   : null;		
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
                'name'     => 'id_edbo',
                'required' => false,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
                'validators' => [
                    0 => ['name'    => 'Zend\I18n\Validator\IsInt'],
                ],                
            ]);
			
            $inputFilter->add([
                'name'     => 'title',
                'required' => true,
                'filters'  => [
                    0 => ['name' => 'StripTags'],
                    1 => ['name' => 'StringTrim'],
                    2 => ['name' => 'Zend\I18n\Filter\Alnum', 'options' => ['allowWhiteSpace' => true]],
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
				'name'     => 'type',
				'required' => true,
				'filters'  => [
					0 => ['name' => 'Zend\Filter\Digits'],
				],
				'validators' => [
					0 => ['name'    => 'Zend\Validator\InArray',
						'options' => [
							'haystack' => [1, 2, 3],
						],
					],
				],                
			]);
			
            $inputFilter->add([
                'name'     => 'period',
                'required' => false,
                'filters'  => [
                    0 => ['name' => 'StripTags'],
                    1 => ['name' => 'StringTrim'],
                    2 => ['name' => 'Zend\I18n\Filter\Alnum', 'options' => ['allowWhiteSpace' => true]],
                ],
                'validators' => [
                    0 => [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 32,
                        ],
                    ],
                    1 => [
                        'name'    => 'Zend\Validator\Regex',
                        'options' => [
                            'pattern' => '/\dр\s\d+м/'
                        ],
                    ]                   
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
                        'name'    => 'Zend\Validator\Between',
                        'options' => ['min' => 2000, 'max' => date('Y')],
                    ],					
                ],				
            ]);

			$inputFilter->add([
                'name'     => 'subject',
                'required' => false,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
                'validators' => [
                    0 => ['name'    => 'Zend\I18n\Validator\IsInt'],
                ],
            ]);
			
			$inputFilter->add([
                'name'     => 'id_level',
                'required' => true,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
                'validators' => [
                    0 => ['name'    => 'Zend\I18n\Validator\IsInt'],
                ],
            ]);
			
            $inputFilter->add([
                'name'     => 'id_specialty',
                'required' => true,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
                'validators' => [
                    0 => ['name'    => 'Zend\I18n\Validator\IsInt'],
                ],
            ]);
			
            $inputFilter->add([
                'name'     => 'id_form',
                'required' => true,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
                'validators' => [
                    0 => ['name'    => 'Zend\I18n\Validator\IsInt'],
                ],
            ]);
			
            $inputFilter->add([
                'name'     => 'id_base',
                'required' => true,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
                'validators' => [
                    0 => ['name'    => 'Zend\I18n\Validator\IsInt'],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'id_school',
                'required' => true,
                'filters'  => [
                    0 => ['name' => 'Zend\Filter\Digits'],
                ],
                'validators' => [
                    0 => ['name'    => 'Zend\I18n\Validator\IsInt'],
                ],
            ]);
			
            $inputFilter->add([
                'name'     => 'min_rate',
                'required' => false,
                'filters'  => [
                    0 => ['name' => 'Zend\I18n\Filter\NumberFormat'],
                ],
				'validators' => [
                    //0 => ['name' => 'Zend\I18n\Validator\IsFloat'],
				],
            ]);
						
            $inputFilter->add([
                'name'     => 'ave_rate',
                'required' => false,
                'filters'  => [
                    0 => ['name' => 'Zend\I18n\Filter\NumberFormat'],
                ],
				'validators' => [
                    //0 => ['name' => 'Zend\I18n\Validator\IsFloat'],
				],
            ]);
						
            $inputFilter->add([
                'name'     => 'max_rate',
                'required' => false,
                'filters'  => [
                    0 => ['name' => 'Zend\I18n\Filter\NumberFormat'],
                ],
				'validators' => [
                    //0 => ['name' => 'Zend\I18n\Validator\IsFloat'],
				],
            ]);

            $inputFilter->add([
                'name'     => 'learning_start',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'Zend\Validator\Date',
                        'options' => ['format' => 'Y-m-d'],
                    ],
                ],				
            ]);

            $inputFilter->add([
                'name'     => 'learning_end',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'Zend\Validator\Date',
                        'options' => ['format' => 'Y-m-d'],
                    ],
                ],				
            ]);
			
            $inputFilter->add([
                'name'     => 'entrance_start',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'Zend\Validator\Date',
                        'options' => ['format' => 'Y-m-d'],
                    ],
                ],				
            ]);
			
            $inputFilter->add([
                'name'     => 'entrance_end',
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'Zend\Validator\Date',
                        'options' => ['format' => 'Y-m-d'],
                    ],
                ],				
            ]);
			
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
