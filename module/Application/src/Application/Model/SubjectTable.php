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
	
	public function save(Subject $subject)
	{
		if(is_array($subject->id)) {
		foreach($subject->id as $id) {
			//$id = $subject->id[$id_subject];
			$data = [
				'required'    => key_exists($id, $subject->required) ? 1 : 0,
				'coefficient' => $subject->coefficient[$id],
				'rating'      => $subject->rating[$id],
				'id_program'  => $subject->id_program,
				'id_subject'  => $subject->id_subject[$id],
			];
				if ($this->fetchOne($id)) {
					$this->update($data, ['id' => $id]);
				} else {
					throw new \Exception('Subject id=' . $subject->id .' does not exist');
				}
			}
		} else {
		    $id = intval($subject->id);
		    $data = [
			    'required'    => $subject->required ? 1 : 0,
			    'coefficient' => $subject->coefficient,
			    'rating'      => $subject->rating,
			    'id_program'  => $subject->id_program,
			    'id_subject'  => $subject->id_subject,
		    ];
    		if ($id == 0) {
	    		$this->insert($data);
	    	} else {
		        if ($this->fetchOne($id)) {
				    $this->update($data, ['id' => $id]);
			    } else {
				    throw new \Exception('Subject id=' . $subject->id .' does not exist');
			    }
		    }
		}
		return true;
	}
	 
	public function deleteSubject($id)
	{
		$this->delete(['id' => (int) $id]);
	}
}