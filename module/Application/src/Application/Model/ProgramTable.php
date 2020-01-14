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
	
	protected $specialtyTable;

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
	
	public function getSpecialtiesId($id_program = false)
    {
		$filter = $id_program ? ['id' => $id_program] : null;
		$programs = $this->select($filter);
		if(!count($programs)) { return false; }
		$ids = [];
		foreach($programs as $program) {
			if(!in_array($program['id_specialty'], $ids)) {
				$ids[] = $program['id_specialty'];
			}
		}
		return $ids;
    }
		
	public function getSpecialties($id_specialty = false)
    {
		$filter = $id_specialty ? ['id' => $id_specialty] : null;
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('specialty');
		if($id_specialty) {
			$select->where($filter);
		}
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		return $resultSet;
    }
			
	public function getProgramsByIdSchool($id_school = false)
    {
		$filter = $id_school ? ['id_school' => $id_school] : null;
		$programs = $this->select($filter);
		if(!count($programs)) { return false; }
		$ids = [];
		foreach($programs as $program) {
			$ids[] = $program['id'];
		}
		return $ids;
    }

	public function getBranch($id_branch)
    {
		if(!$id_branch) { return false; }
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('branch')->where(['id' => $id_branch]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current();
    }

	public function getSchool($id_school)
    {
		if(!$id_school) { return false; }
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('school')->where(['id' => $id_school]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current();
    }
	
	public function getLevels($locale)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['id', 'title_' . $locale])->from('level');
        $selectString = $sql->buildSqlString($select);
		$levels = [];
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		foreach($resultSet as $result) {
			$levels[$result->id] = $result->{'title_'.$locale};
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

	// return All Subjects
	public function getSubjects()
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('subject');
        $selectString = $sql->buildSqlString($select);
		$subjects = [];
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		foreach($resultSet as $result) {
			$subjects[$result->id] = $result;
		}
        return $subjects;
    }
	
	// return Options of Subjects needed to this Program
	public function getExamSubjectOptions($id_program)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('program_has_subject')->where(['id_program' => $id_program])->order(['id_subject']);
        $selectString = $sql->buildSqlString($select);
		$subjects = [];
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		foreach($resultSet as $result) {
			$subjects[$result->id_subject] = $result;
		}
        return $subjects;
    }
	
	// return Subjects needed to this Program
	public function getExamSubjects($id_program)
    {
		$options = $this->getExamSubjectOptions($id_program);
		$ids = array_keys($options);
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('subject')->where(['id' => $ids])->order(['title']);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		$subjects = [];
		foreach($resultSet as $result) {
			$subjects[$result->id]['title'] = $result->title;
			$subjects[$result->id]['required'] = $options[$result->id]['required'];
			$subjects[$result->id]['coefficient'] = $options[$result->id]['coefficient'];
			$subjects[$result->id]['rating'] = $options[$result->id]['rating'];
		}
        return $subjects;
    }
	
	public function getSpecialtyDOM($id_school = false, $locale)
    {
		$domDocument = new \DomDocument('1.0', 'utf-8');
        $domRoot = $domDocument->createElement('universities');
		$domDocument->appendChild($domRoot);
		$domElement = $domDocument->createElement('university');
		$domRoot->appendChild($domElement);			
		$domAttribute = $domDocument->createAttribute('id');
		$domAttribute->value = 'university_' . intval($id_school);
		$domElement->appendChild($domAttribute);
		if($id_school) {
			$school = $this->getSchool($id_school);
			$domAttribute = $domDocument->createAttribute('name');
			$domAttribute->value = $school->{'name_' . $locale};
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('shortname');
			$domAttribute->value = $school->shortname;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('http');
			$domAttribute->value = $school->http;
			$domElement->appendChild($domAttribute);
		}
		$branches = $domDocument->createElement('branches');
		$domElement->appendChild($branches);
		$id_programs = $this->getProgramsByIdSchool($id_school);
		$programs = $this->fetch($id_programs);
		$id_specialties = $this->getSpecialtiesId($id_programs);
		$specialties = $this->getSpecialties($id_specialties);
		$forms = $this->getForms();
		$levels = $this->getLevels($locale);
		$subjects = $this->getSubjects();
		$id_branch = [];
		foreach($specialties as $specialty) {
			if(!in_array($specialty->id_branch, $id_branch)) {
				$id_branch[] = $specialty->id_branch;
			}
		}
		foreach($id_branch as $id) {
			$branch = $this->getBranch($id);
			$domElement = $domDocument->createElement('branch', $branch->title);
			$branches->appendChild($domElement);
			$domAttribute = $domDocument->createAttribute('id');
            $domAttribute->value = 'branch_' . $branch->id;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('code');
            $domAttribute->value = sprintf("%02d", $branch->code);
			$domElement->appendChild($domAttribute);
			$domElement->setIdAttribute('id', true);
			$child = $domDocument->createElement('specialties');
			$domElement->appendChild($child);
		}
		$specialties = $this->getSpecialties($id_specialties);
		foreach($specialties as $specialty) {
			$branch = $domDocument->getElementById('branch_' . $specialty->id_branch);
			$parent = $branch->getElementsByTagName('specialties')[0];
			$domElement = $domDocument->createElement('specialty', $specialty->title);
			$parent->appendChild($domElement);
			$domAttribute = $domDocument->createAttribute('id');
			$domAttribute->value = 'specialty_' . $specialty->id;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('code');
			$domAttribute->value = sprintf("%03d", $branch->getAttribute('code') . $specialty->code);
			$domElement->appendChild($domAttribute);
			$domElement->setIdAttribute('id', true);
			$child = $domDocument->createElement('programs');
			$domElement->appendChild($child);
		}
		foreach($programs as $program) {
			$specialty = $domDocument->getElementById('specialty_' . $program['id_specialty']);
			$parent = $specialty->getElementsByTagName('programs')[0];
			$domElement = $domDocument->createElement('program', $program->title);
			$parent->appendChild($domElement);
			$domAttribute = $domDocument->createAttribute('id');
			$domAttribute->value = 'program_' . $program->id;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('period');
			$domAttribute->value = $program->period;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('year');
			$domAttribute->value = $program->year;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('id_form');
			$domAttribute->value = $program->id_form;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('form_title');
			$domAttribute->value = $forms[$program->id_form];
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('id_level');
			$domAttribute->value = $program->id_level;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('level_title');
			$domAttribute->value = $levels[$program->id_level];
			$domElement->appendChild($domAttribute);
			$examSubjects = $this->getExamSubjectOptions($program->id);
			if($examSubjects) {
				$child = $domDocument->createElement('subjects');
				$domElement->appendChild($child);
				foreach($examSubjects as $id_subject => $subject) {
					$domElement = $domDocument->createElement('subject', $subjects[$id_subject]['title']);
					$child->appendChild($domElement);
					$domAttribute = $domDocument->createAttribute('id');
					$domAttribute->value = 'subject_' . $id_subject;
					$domElement->appendChild($domAttribute);
					$domAttribute = $domDocument->createAttribute('required');
					$domAttribute->value = $subject['required'];
					$domElement->appendChild($domAttribute);
					$domAttribute = $domDocument->createAttribute('coefficient');
					$domAttribute->value = $subject['coefficient'];
					$domElement->appendChild($domAttribute);
					$domAttribute = $domDocument->createAttribute('rating');
					$domAttribute->value = $subject['rating'];
					$domElement->appendChild($domAttribute);				
				}
			}
		}
		return $domDocument;
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
		return $this->delete(['id' => (int) $id]);
	}

}