<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class ProgrambachTable extends AbstractTableGateway
{

    public $adapter;
	public $table;
	
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet();
        $this->table = 'bach_programs';
        $this->initialize();
    }

    public function fetchAll()
    {
		$rowset = $this->select(function (Select $select) {
			$select->order('UniversityId');
		});
		return $rowset->toArray();
    }

	public function fetch($id)
    {
		return $this->select(['id' => $id])->current();
    }
	
	public function getSchoolIdEdbo()
    {
		$rowset = $this->select(function (Select $select) {
			$select->columns(['UniversityId'], false);
		});
		$schools = [];
		foreach($rowset as $item) {
			if(!in_array($item->UniversityId, $schools)) {
				$schools[] = $item->UniversityId;
			}
		}
		return $schools;
    }
	
	public function getProgram($id_school)
    {
		return $this->select(['UniversityId' => $id_school]);
    }
}