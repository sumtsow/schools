<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class SubjectTable extends AbstractTableGateway
{
    protected $table ='program_has_subject';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->initialize();
    }
    
    public function fetchAll()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select()->from('subject');
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		return $resultSet->toArray();
    }

    public function fetchByLevel($id_level)
    {
        $id_level = intval($id_level);

	$sql1 = new Sql($this->adapter);
        $select1 = $sql1->select()->columns(['id'])->from('program')->where(['id_level' => $id_level]);

	$sql2 = new Sql($this->adapter);
	$select2 = $sql2->select()->columns(['id_subject'])->from('program_has_subject')->where(['id_program IN(?)' => $select1]);


	$sql3 = new Sql($this->adapter);
	$select3 = $sql3->select()->from('subject')->where(['id IN(?)' => $select2]);			
        $selectString = $sql3->buildSqlString($select3);
	$result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE)->toArray();	
		
	return $result;
    }
	
    public function fetch($id)
    {
		return $this->select(['id' => $id])->current();
    }
    
	public function fetchOne($id)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('subject')->where(['id' => $id]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		return $resultSet;
    }
	
	public function getIdSubjectByIdEDBO($id_edbo)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['id'])->from('subject')->where(['id_edbo' => $id_edbo]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current()->id;
    }
	
	public function getSubjects()
    {
		$results = $this->fetchAll();
		$subjects = [];
		foreach($results as $result) {
			$subjects[$result['id']] = $result['title'];
		}
		return $subjects;
    }
	
	public function getAllSubjects($id_program)
    {
		return $this->select(['id_program' => $id_program]);
    }
	
	// return Options of Subject
	public function getSubjectOptions($id_program, $id_subject)
    {
		return $this->select(['id_program' => $id_program, 'id_subject' => $id_subject], 'AND')->current();
    }
	
	
	public function importSubjectFromJson($id_program, $id_edbo, $offers_subjects)
    {
		$key = 0;
		$subject = new Subject();
		$subject->id_program = intval($id_program);		
		foreach($offers_subjects->{$id_edbo} as $offers_subject) {
			$subject->id_row[$key] = 0;			
			$subject->id_subject[$key] = intval($this->getIdSubjectByIdEDBO($offers_subject[0]));
			if(!$offers_subject[4]) $subject->required[$key] = 1;
			$subject->coefficient[$key] = floatval($offers_subject[3]);
			$subject->rating[$key] = floatval($offers_subject[5]);
			$key++;
		}
		$this->save($subject);
        return $subject;
    }

	public function save(Subject $subject)
	{
		if(is_array($subject->id_row)) {
		$old_subjects = $this->getAllSubjects($subject->id_program);
		foreach($old_subjects as $row) {
			$this->deleteSubject($row->id);
		}
		foreach($subject->id_row as $key => $id) {
			$data = [
				'required'    => ($subject->required[$key]) ? ($subject->required[$key]) : 0,
				'coefficient' => $subject->coefficient[$key],
				'rating'      => $subject->rating[$key],
				'id_program'  => $subject->id_program,
				'id_subject'  => $subject->id_subject[$key],
			];
				if ($this->fetch($id)) {
					$this->update($data, ['id' => $id]);
				} else {
					$this->insert($data);
				}
			}
		} else {
		    $id = intval($subject->id_row);
		    $data = [
			    'required'    => $subject->required,
			    'coefficient' => $subject->coefficient,
			    'rating'      => $subject->rating,
			    'id_program'  => $subject->id_program,
			    'id_subject'  => $subject->id_subject,
		    ];
    		if ($id == 0) {
	    		$this->insert($data);
	    	} else {
		        if ($this->fetch($id)) {
				    $this->update($data, ['id' => $id]);
			    } else {
				    throw new \Exception('Subject id=' . $subject->id .' does not exist');
			    }
		    }
		}
		unset($data);
		return true;
	}
	 
	public function deleteSubject($id)
	{
		$this->delete(['id' => (int) $id]);
	}
}
