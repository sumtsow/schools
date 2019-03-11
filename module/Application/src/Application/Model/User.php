<?php
namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Authentication\Adapter\Digest as AuthAdapter;
use Zend\Authentication\AuthenticationService as AuthService;

class User implements InputFilterAwareInterface
{
	const FNAME = '.htaccess';
        const REALM = 'admin';
        protected $login;
        protected $realm;        
        protected $auth;
        protected $inputFilter;

    public function __construct() {
        $this->auth = new AuthService();
	$identity = $this->auth->getIdentity();
        $this->login = $identity['username'];
        $this->realm = $identity['realm'];
    }
    
    static function getDocumentRoot()
    {
         preg_match("/[^\/\/]+$/", $_SERVER['DOCUMENT_ROOT'],$matches);
         return $matches[0];
    }
    
    public function login($login,$passwd,$dir) {
        if($this->auth->hasIdentity()) {
            $result = $this->auth->getIdentity();
        }
        else {
            $path = $dir.'/'.self::FNAME;
            if(file_exists($path)) {        
                $config = file($path);            
            }
            foreach($config as $row) {
                // Если строка содержит текст и не закомментирована
                if ((!preg_match("/^#/", $row)) && preg_match("/AuthUserFile/",$row)) {
                    $row = trim($row);
                    preg_match('/"(.*)"/',$row,$matches);
                    $path = $matches[1]; 
                    break;
                }
            }
            $adapter = new AuthAdapter($path, self::REALM, $login, $passwd);        
            $result = $this->auth->authenticate($adapter);
            if($result->isValid()) {
                $identity = $result->getIdentity();
                $this->login = $identity['username'];
            }
        }
        return $result;
    }
    
    public function logout()
    {
        $this->auth->clearIdentity();
	session_unset();
	session_destroy();
	session_start();
	setcookie('PHPSESSID',NULL,0,'/');
        return true;
    }
    
    public function isValid()
    {
        $result = false;
        if ($this->auth->hasIdentity()) {
            $result = true;
        }
        return $result;
    }
    
    public function __get($property)
    {
        return $this->$property;
    }  
    
    public function exchangeArray($data)
    {
        $this->login = (isset($data['login'])) ? $data['login'] : null;
        $this->passwd = (isset($data['passwd'])) ? $data['passwd'] : null;
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
                'name'     => 'login',
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

        return $this->inputFilter = $inputFilter;
    }
  }
}
