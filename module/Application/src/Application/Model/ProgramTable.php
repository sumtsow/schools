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
		$programs = $this->select(['id_specialty' => $id_specialty])->toArray();
		if(!count($programs)) { return false; }
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
	
	public function getSpecialtiesId($id)
    {
		$programs = $this->select(['id' => $id]);
		if(!count($programs)) { return false; }
		$ids = [];
		foreach($programs as $program) {
			$ids[] = $program['id_specialty'];
		}
		return $ids;
    }
	
	public function getLevels($lang)
    {
		//$this->getServiceLocator()->get('translator')->getLocale();
		//$lang = $this->getApplication()->getServiceManager()->get('translator')->getLocale();
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
}