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
	
	public function getProgramsBySchool($id_school)
    {
		return $this->select(['UniversityId' => $id_school]);
    }
	
	public function checkProgramExists($id_edbo)
    {
		$sql = new Sql($this->adapter);
		$select = $sql->select()->columns(['id'])->from('program')->where(['id_edbo' => $id_edbo])->limit(1);
        $selectString = $sql->buildSqlString($select);
		$result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		return ($result->current()) ? $result->current()->id : false;
    }

	public function getIdLevelByIdEDBO($id_edbo)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['id'])->from('level')->where(['id_edbo' => $id_edbo]);
        $selectString = $sql->buildSqlString($select);
		$result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return ($result->current()) ? $result->current()->id : false;
    }

	public function getIdSpecialtyByIdEDBO($id_edbo)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['id'])->from('specialty')->where(['id_edbo' => $id_edbo]);
        $selectString = $sql->buildSqlString($select);
		$result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return ($result->current()) ? $result->current()->id : false;
    }

	public function getIdFormByIdEDBO($id_edbo)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['id'])->from('form')->where(['id_edbo' => $id_edbo]);
        $selectString = $sql->buildSqlString($select);
		$result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return ($result->current()) ? $result->current()->id : false;
    }

	public function getIdBaseByIdEDBO($id_edbo)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['id'])->from('base')->where(['id_edbo' => $id_edbo]);
        $selectString = $sql->buildSqlString($select);
		$result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return ($result->current()) ? $result->current()->id : false;
    }
	
	public function getSchoolId($id_edbo)
    {
		$sql = new Sql($this->adapter);
		$select = $sql->select()->columns(['id'])->from('school')->where(['id_edbo' => $id_edbo])->limit(1);
        $selectString = $sql->buildSqlString($select);
		$result = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		return ($result->current()) ? $result->current()->id : false;
    }

	public function getProgram($id_edbo)
    {
		if(!$id_edbo) { return; }
		$data = $this->fetch($id_edbo);
		$id = $this->checkProgramExists($id_edbo);
		$program['id'] = $id ? $id : 0;
		$program['id_edbo'] = $id_edbo;
		$program['title'] = $data->Name;
		$program['type'] = 1;
		$program['period'] = $data->StudyTerm;
		$program['year'] = '2019';
		$program['learning_start'] = $data->StudyDateFrom;
		$program['learning_end'] = $data->StudyDateTo;
		$program['entrance_start'] = $data->TicketDateFrom;
		$program['entrance_end'] = $data->TicketDateTo;
		$program['id_level'] = $this->getIdLevelByIdEDBO($data->QualificationId);
		$program['id_specialty'] = $this->getIdSpecialtyByIdEDBO($data->SpecialityId);
		$program['id_form'] = 1;
		$program['id_school'] = $this->getSchoolId($data->UniversityId);
		$program['id_base'] = $this->getIdBaseByIdEDBO($data->EducationBaseId);
		$program['min_rate'] = $data->ScoreMin;
		$program['ave_rate'] = $data->ScoreAverage;
		$program['max_rate'] = $data->ScoreMax;	
		return $program;
    }
}