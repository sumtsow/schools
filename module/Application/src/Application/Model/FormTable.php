<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class FormTable extends AbstractTableGateway
{
    protected $table ='form';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->initialize();
    }
    
    public function fetchAll()
    {
        return $this->select();
    }
    
    public function find($id)
    {
        return $this->select(['id' => $id]);
    }
}