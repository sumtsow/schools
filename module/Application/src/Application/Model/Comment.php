<?php
namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Comment implements InputFilterAwareInterface
{
	public $id;
	public $id_school;	
	public $author;
	public $time;
	public $text;
	public $visible;	
        protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id        = (isset($data['id'])) ? $data['id']     : null;
        $this->id_school = (isset($data['id_school'])) ? $data['id_school'] : null;
        $this->author    = (isset($data['author'])) ? $data['author'] : null;
        $this->time      = (isset($data['time'])) ? $data['time']     : null;
        $this->text      = (isset($data['text'])) ? $data['text'] : null;        
        $this->visible   = (isset($data['visible'])) ? $data['visible']  : null;
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
                'required' => false,
                'filters'  => array(
                    array('name' => 'ToInt'),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'id_school',
                'required' => false,
                'filters'  => array(
                    array('name' => 'ToInt'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'author',
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
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
			
            $inputFilter->add($factory->createInput(array(
                'name'     => 'text',
                'required' => false,
                'filters'  => array(
                    //array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 1023,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'visible',
                'required' => false,
            )));
			
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
    public function censorComment($text)
    {
        $pattern = file(User::getDocumentRoot()."/filter.txt");
        foreach($pattern as $row) {
            $word = trim($row);
            $num = strlen($word);
            $repl = ""; 
            for($i=0;$i<$num/2;$i++) {
                $repl .= "*";
            }
        $text = str_replace($word,$repl,$text);
        }
        return $text;
    }
    
    public function insertSmile()
    {
        $text = $this->text;
        $text = str_replace(":))","<img src='/img/biggrin.gif' border=0>",$text);
        $text = str_replace(":P)","<img src='/img/razz.gif' border=0>",$text);
        $text = str_replace(":-)","<img src='/img/cool.gif' border=0>",$text);
        $text = str_replace(":-(","<img src='/img/fury.gif' border=0>",$text);
        $text = str_replace(":((","<img src='/img/redface.gif' border=0>",$text);
        $text = str_replace(";))","<img src='/img/wink.gif' border=0>",$text);
        $text = str_replace(":roll;","<img src='/img/rolleyes.gif' border=0>",$text);
        $text = str_replace(":rf;","<img src='/img/confused.gif' border=0>",$text);
        $text = str_replace(")-`","<img src='/img/eek.gif' border=0>",$text);
        $text = str_replace("`-(","<img src='/img/cry.gif' border=0>",$text);
        $text = str_replace(":)","<img src='/img/smile.gif' border=0>",$text);
        $text = str_replace(":+(","<img src='/img/angry.gif' border=0>",$text);
        return $text;
    }    
}