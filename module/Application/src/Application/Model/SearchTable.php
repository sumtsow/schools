<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class SearchTable extends AbstractTableGateway
{

    public $adapter;
	public $table;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->table = 'program_has_subject';
        $this->resultSetPrototype = new ResultSet();
        $this->initialize();
    }
	    
    public function fetchAll()
    {
        $this->table = 'program_has_subject';
        return $this->select();
    }
	
    public function getPrograms($subjects, $level, $form = false)
    {
		if(!$subjects || !is_array($subjects)) { return false; }		
        $result = $this->findProgramsBySubjectId($subjects);
        $cond = $this->parseToStringArray($result, $level);
        $programs = $this->fetchPrograms($cond, $level);
        foreach($programs as $key => $program) {
            $programs[$key]['form_title'] = $this->getFormTitle($program['id_form']);
            $programs[$key]['speciality_title'] = $this->getSpecialityTitle($program['id_speciality']);
			$programs[$key]['universities'] = $this->getUniversitiesByProgramId($program['id']);
        }
        return $programs;
    }
    
    public function parseToStringArray($params, $level)
    {
        $columnArray = array_column($params, 'id_program');
        $strArray = [];
		$and = '';
		if($level) {
			$and = ' AND id_level=' . $level;
		}
        foreach($columnArray as $item) {
            $strArray[] = 'id=' . $item . $and;
		}
        return $strArray;
    }
    
    public function findProgramsBySubjectId($subjects)
    {
		$sql = '';
		$union = '';
		foreach($subjects as $id) {
			$sql .= $union . 'SELECT `id_program` FROM `' . $this->table . '` WHERE `id_subject`=' . intval($id);			
			if(!$union) { $union = ' UNION '; }
		}
        $resultSet = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        return $resultSet->toArray();
    }
    
    public function fetchPrograms($cond, $level = 0)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('program');
        $select->where($cond, 'OR');
        $selectString = $sql->buildSqlString($select);
        $result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();
    }
    
    public function getFormTitle($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('form');
		$select->columns(['title']);
		$select->where(['id' => $id]);
        $selectString = $sql->buildSqlString($select);
        $result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		$result = $result->toArray();
        return $result[0]['title'];
    }
    
    public function getSpecialityTitle($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('speciality');
		$select->columns(['title']);
		$select->where(['id' => $id]);
        $selectString = $sql->buildSqlString($select);
        $result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		$result = $result->toArray();
        return $result[0]['title'];
    }
	    
    public function getUniversitiesByProgramId($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('school_has_program');
		$select->columns(['id_school']);
        $select->where(['id_program' => $id]);
        $selectString = $sql->buildSqlString($select);
        $result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        $ids = $result->toArray();
		$schools = [];
		foreach($ids as $id_school) {
			$schools[] = $this->getUniversityById($id_school['id_school'])[0];
		}
		return $schools;
    }
	
	public function getUniversityById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('school');
		$select->columns(['id', 'name_uk', 'name_en', 'name_ru', 'shortname', 'http']);
        $select->where('id=' . $id . ' AND high=1');
        $selectString = $sql->buildSqlString($select);
        $result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		return $result->toArray();
    }
}