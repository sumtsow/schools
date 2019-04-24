<?php

namespace Application\Model;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

class Mail extends Message {
    
    public function sendMessage($text) {
        
       
        $this->addFrom('admin@schools.kharkov.ua', 'Школы Харькова')
        ->addTo('sumtsow@gmail.com')
        ->setSubject('Новый комментарий на сайте Школы Харькова');
        
        $this->setBody($text);
        
        $transport = new SmtpTransport();
        
        $options   = new SmtpOptions(array(
            'name'              => 'mail.schools.kharkov.ua',
            'host'              => 'mail.schools.kharkov.ua',
            'port' => 26,
            'connection_class'  => 'login',
            'connection_config' => array(
                'username' => 'admin@schools.kharkov.ua',
                'password' => 'Cyi}ryN4mF6h',
            ),
        ));
        
        $transport->setOptions($options);
        
        $transport->send($this);
    }
            
}
