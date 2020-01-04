<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;

class ProgramTable extends AbstractTableGateway
{

    public $adapter;
	public $table;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet();
        $this->table = 'program';
        $this->initialize();
    }
	    
    public function fetchAll()
    {
		return $this->select();
    }
	
	public function fetchOne($id)
    {
		return $this->select(['id' => $id])->current();
    }
		
	public function getSchools($id_specialty)
    {
		if(!$id_specialty) { return false; }
		$programs = $this->select(['id_specialty' => $id_specialty])->toArray();
		if(!$programs) { return false; }
		$ids = [];
		foreach($programs as $program) {
			$ids[] = $program['id'];
		}
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('school_has_program')->columns(['id_school'])->where(['id_program' => $ids]);
        $selectString = $sql->buildSqlString($select);
        $schools = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		$ids = [];
		foreach($schools as $school) {
			if(!in_array($school['id_school'], $ids)) {
				$ids[] = $school['id_school'];
			}
		}
		return $ids;
    }	
}