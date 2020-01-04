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
        return $this->select();
    }
	
    public function getPrograms($rating, $level, $form = false)
    {
		if(!$rating || !is_array($rating)) { return false; }
        $result = $this->findProgramsByRating($rating);
        if(!$result) return false;
		$cond = $this->parseToStringArray($result, $level, $form);
        $programs = $this->fetchPrograms($cond);
        foreach($programs as $key => $program) {
            $programs[$key]['form_title'] = $this->getFormTitle($program['id_form']);
            $programs[$key]['specialty_title'] = $this->getSpecialtyTitle($program['id_specialty']);
			$programs[$key]['universities'] = $this->getUniversitiesByProgramId($program['id']);
        }
        return $programs;
    }
    
    public function parseToStringArray($params, $level, $form = false)
    {
        $strArray = [];
		$and = '';
		if($level) {
			$and = ' AND id_level=' . $level;
		}
		if($form) {
			$and .= ' AND id_form=' . $form;
		}
        foreach($params as $item) {
            $strArray[] = '(id=' . $item . $and . ')';
		}
        return $strArray;
    }
    
    public function findProgramsByRating($rating)
    {
		$id_subject = array_keys($rating);
		$key_str = implode(',', $id_subject);
		$sql = 'SELECT `id_program` FROM `program_has_subject` WHERE `id_subject` IN (' . $key_str . ')  GROUP BY `id_program`';
		$resultSet = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        $programs = $resultSet->toArray();
        $result = [];
        foreach($programs as $program) {
			$sql = 'SELECT `id_subject`,`rating`,`required` FROM `' . $this->table . '` WHERE `id_program`=' . $program['id_program'];
			$resultSet = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
			$subjects = $resultSet->toArray();
			$passed = 0;
			$required = 0;
		    foreach($subjects as $subject) {
				if(array_key_exists($subject['id_subject'], $rating) && $rating[$subject['id_subject']] >= $subject['rating']) {
				    $passed++;
			    }
				if($subject['required']) {
					$required++;
				}
		    }
		    if($passed > 2 && $required == 2) {
			     $result[] = $program['id_program'];
			}
        }
        return $result;
    }
    
    public function fetchPrograms($cond)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select()->from('program')->where($cond, 'OR');
        $selectString = $sql->buildSqlString($select);
        $result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();
    }
    
    public function getFormTitle($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('form')->columns(['title'])->where(['id' => $id]);
        $selectString = $sql->buildSqlString($select);
        $result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		$result = $result->toArray();
        return $result[0]['title'];
    }
    
    public function getSpecialtyTitle($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('specialty')->columns(['title'])->where(['id' => $id]);
        $selectString = $sql->buildSqlString($select);
        $result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		$result = $result->toArray();
        return $result[0]['title'];
    }
	    
    public function getUniversitiesByProgramId($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('school_has_program')->columns(['id_school'])->where(['id_program' => $id]);
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
        $select->from('school')->columns(['id', 'name_uk', 'name_en', 'name_ru', 'shortname', 'http'])->where('id=' . $id . ' AND high=1');
        $selectString = $sql->buildSqlString($select);
        $result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		return $result->toArray();
    }
}