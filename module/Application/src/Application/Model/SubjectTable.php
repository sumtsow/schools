<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class SubjectTable extends AbstractTableGateway
{
    protected $table ='subject';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Subject());
        $this->initialize();
    }
    
    public function fetchAll()
    {
        return $this->select();
    }
}