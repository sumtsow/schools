<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;

class RegionTable extends AbstractTableGateway
{
    protected $table ='region';

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
    
    public function fetch($id)
    {
		return $this->select(['id' => $id])->current();
    }
}