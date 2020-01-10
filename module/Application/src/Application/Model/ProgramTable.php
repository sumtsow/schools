<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
//use Zend\Db\TableGateway\TableGateway;
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
	
	public function fetch($id)
    {
		return $this->select(['id' => $id]);
    }
	
	public function getPrograms($id)
    {
		$titles = [];
		$programs = $this->fetch($id);
		$levels = $this->getLevels('uk');
		$forms = $this->getForms();
		foreach($programs as $program) {
			$titles[$program->id] = $program->title . ', ' . $levels[$program->id_level] . ', '. $forms[$program->id_form];
		}
		return $titles;
    }
	
	public function getSchools($id_specialty)
    {
		if(!$id_specialty) { return false; }
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('program')->columns(['id_school'])->where(['id_specialty' => $id_specialty]);
        $selectString = $sql->buildSqlString($select);
        $schools = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE)->toArray();
		if(!$schools) { return false; }
		$ids = [];
		foreach($schools as $school) {
			if(!in_array($school['id_school'], $ids)) {
				$ids[] = $school['id_school'];
			}
		}
		return $ids;
    }
	
	public function getSpecialtiesId($id_specialty)
    {
		$programs = $this->select(['id' => $id_specialty]);
		if(!count($programs)) { return false; }
		$ids = [];
		foreach($programs as $program) {
			$ids[] = $program['id_specialty'];
		}
		return $ids;
    }
			
	public function getProgramsByIdSchool($id_school)
    {
        $programs = $this->select(['id_school' => $id_school]);
		if(!count($programs)) { return false; }
		$ids = [];
		foreach($programs as $program) {
			$ids[] = $program['id'];
		}
		return $ids;
    }
	
	public function getLevels($lang)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['id', 'title_' . $lang])->from('level');
        $selectString = $sql->buildSqlString($select);
		$levels = [];
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		foreach($resultSet as $result) {
			$levels[$result->id] = $result->{'title_'.$lang};
		}
        return $levels;
    }
		
	public function getForms()
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('form');
        $selectString = $sql->buildSqlString($select);
		$forms = [];
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		foreach($resultSet as $result) {
			$forms[$result->id] = $result->title;
		}
        return $forms;
    }

	public function saveProgram(Program $program)
	{
		$data = [
			'title' => $program->title,
			'period' => $program->period,
			'year' => $program->year,
			'id_level' => $program->id_level,
			'id_specialty' => $program->id_specialty,
			'id_form' => $program->id_form,
			'id_school' => $program->id_school,
		];
		$id = intval($program->id);
		if ($id == 0) {
			$this->insert($data);
		} else {
			if ($this->fetchOne($id)) {
				$this->update($data, ['id' => $id]);
			} else {
				throw new \Exception('Program id does not exist');
			}
		}
		return true;
	}
	 
     public function deleteProgram($id)
     {
         $this->delete(['id' => (int) $id]);
     }
}