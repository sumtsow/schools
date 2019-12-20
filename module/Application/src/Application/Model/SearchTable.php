<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class SearchTable extends AbstractTableGateway
{

    protected $adapter;
	protected $table = 'program_has_subject';

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
	
    public function getPrograms($subjects)
    {
		if(!count($subjects)) { return false; }
		$filter = '';
		$and = '';
		foreach($subjects as $id) {
			$filter .= $and . '`id_subject`=' . intval($id);			
			if(!$and) { $and = ' AND '; }
		}
		$sql = 'SELECT `id_program` FROM `program_has_subject` WHERE `id_subject`=5 UNION SELECT `id_program` FROM `program_has_subject` WHERE `id_subject`=6 UNION SELECT `id_program` FROM `program_has_subject` WHERE `id_subject`=7';
		//$this->table = 'program_has_subject';
		//$resultSet = $this->select($filter);
		//return $resultSet;
		return $filter;
    }
}