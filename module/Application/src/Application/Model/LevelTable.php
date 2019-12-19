<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class LevelTable extends AbstractTableGateway
{
    protected $table ='level';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Level());
        $this->initialize();
    }
    
    public function fetchAll()
    {
        return $this->select();
    }
}