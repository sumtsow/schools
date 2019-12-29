<?php

//namespace Zend;
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;

class SpecialtyTable extends AbstractTableGateway
{

    public $adapter;
	public $table;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet();
        $this->table = 'specialty';
        $this->initialize();
    }
	    
    public function fetchAll()
    {
		return $this->select();
    }
	
	public function fetchBranches()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('branch');
        $selectString = $sql->buildSqlString($select);
        $resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $resultSet;
    }
	
	public function getBranchesDOM()
    {
        $domDocument = new \DomDocument('1.0', 'utf-8');
        $domRoot = $domDocument->createElement('branches');
        $domDocument->appendChild($domRoot);
		$specialties = self::fetchAll();
		foreach(self::fetchBranches() as $branch) {
			$domElement = $domDocument->createElement('branch', sprintf("%02d", $branch->code) . ': ' . $branch->title);
			$domRoot->appendChild($domElement);
			$domAttribute = $domDocument->createAttribute('id');
            $domAttribute->value = 'branch_' . $branch->id;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('code');
            $domAttribute->value = $branch->code;
			$domElement->appendChild($domAttribute);
			$domElement->setIdAttribute('id', true);
			$child = $domDocument->createElement('specialties');
			$domElement->appendChild($child);
		}
		foreach($specialties as $specialty) {
			$branch = $domDocument->getElementById('branch_' . $specialty->id_branch);
			$parent = $branch->getElementsByTagName('specialties')[0];
			$domElement = $domDocument->createElement('specialty', sprintf("%03d", $branch->getAttribute('code') . $specialty->code) . ': ' . $specialty->title);
			$parent->appendChild($domElement);
			$domAttribute = $domDocument->createAttribute('id');
			$domAttribute->value = $specialty->id;
			$domElement->appendChild($domAttribute);
		}
		return $domDocument;
    }
}