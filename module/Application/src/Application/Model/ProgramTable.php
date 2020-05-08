<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
//use Application\Model\Program;

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

	public function fetch($id)
    {
		return $this->select(['id' => $id]);
    }
	
	public function fetchIdEDBO($id_edbo)
    {
		return $this->select(['id_edbo' => $id_edbo])->current();
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

	public function getIdLevelByIdEDBO($id_edbo)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['id'])->from('level')->where(['id_edbo' => $id_edbo]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current()->id;
    }

	public function getIdSpecialtyByIdEDBO($id_edbo)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['id'])->from('specialty')->where(['id_edbo' => $id_edbo]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current()->id;
    }

	public function getIdFormByIdEDBO($id_edbo)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['id'])->from('form')->where(['id_edbo' => $id_edbo]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current()->id;
    }

	public function getRegionTitle($id_region)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['title'])->from('region')->where(['id' => $id_region]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current()->title;
    }
	
	public function getIdBaseByIdEDBO($id_edbo)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['id'])->from('base')->where(['id_edbo' => $id_edbo]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current()->id;
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
	
	public function getBases()
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('base');
        $selectString = $sql->buildSqlString($select);
		$bases = [];
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		foreach($resultSet as $result) {
			$bases[$result->id] = $result->title;
		}
        return $bases;
    }
    
    public function getTypes()
    {
        $resultSet = $this->adapter->query('DESCRIBE `program` `type`', Adapter::QUERY_MODE_EXECUTE);
        $resultArr = $resultSet->toArray();
        $resultArr = explode(',',str_replace("'","",substr($resultArr[0]['Type'],5,-1)));
		array_unshift($resultArr, null);		
		//array_pop($resultArr);
		unset($resultArr[0]);
        return $resultArr;
    }
	
	// return All Subjects
	public function getSubjectTitles()
    {
        $subjects = $this->getSubjects();
        $titles = [];
		foreach($subjects as $subject) {
			$titles[$subject->id] = $subject->title;
		}
        return $titles;
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
        $select = $sql->select()->from('program_has_subject')->where(['id_program' => $id_program])->order(['required DESC']);
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
		if(!count($options)) { return; }		
		$ids = array_keys($options);
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('subject')->where(['id' => $ids]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		$subjects = [];
		foreach($resultSet as $result) {
			$subject = [
				'id' => $result->id,
				'title' => $result->title,				
				'id_row' => $options[$result->id]['id'],
				'required' => $options[$result->id]['required'],
				'coefficient' => $options[$result->id]['coefficient'],
				'rating' => $options[$result->id]['rating'],
				'optional' => $options[$result->id]['optional'],
			];
			if($subject['required']) {
				array_unshift($subjects, $subject);
			} else {
				array_push($subjects, $subject);
			}
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
			$domAttribute = $domDocument->createAttribute('email');
			$domAttribute->value = $school->email;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('http');
			$domAttribute->value = $school->http;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('logo');
			$domAttribute->value = $school->logo;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('address');
			$domAttribute->value = $school->address;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('phone');
			$domAttribute->value = $school->phone;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('info');
			$domAttribute->value = $school->info;			
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('area');
			$domAttribute->value = $school->area;			
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('id_region');
			$domAttribute->value = $school->id_region;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('region');
			$domAttribute->value = $this->getRegionTitle($school->id_region);
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('high');
			$domAttribute->value = $school->high;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('map');
			$domAttribute->value = $school->map;
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
			$domAttribute = $domDocument->createAttribute('min_rate');
			$domAttribute->value = $program->min_rate;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('ave_rate');
			$domAttribute->value = $program->ave_rate;
			$domElement->appendChild($domAttribute);
			$domAttribute = $domDocument->createAttribute('max_rate');
			$domAttribute->value = $program->max_rate;
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
					$domAttribute = $domDocument->createAttribute('optional');
					$domAttribute->value = $subject['optional'];
					$domElement->appendChild($domAttribute);
				}
			}
		}
		return $domDocument;
	}

    public function importProgramFromJson($id_school, $id_edbo, $json, $key)
    {
		$offer = $json->offers[$key];
		$old_program = $this->fetchIdEDBO($id_edbo);
		$program = new Program();
		if($old_program) {
			$program->exchangeArray($old_program);
		} else {
			$program->id = 0;
		}
		$program->id_edbo = $offer[0];
		$program->title = $offer[20];
		$program->type = $offer[19] + 1;
		$program->period = $offer[9];
		$program->year = substr($offer[10], -4);
		$program->license_num = $offer[16];
		$program->contract_num = $offer[17];
		$program->learning_start = \DateTime::createFromFormat('d.m.Y', $offer[10])->format('Y-m-d');
		$program->learning_end = \DateTime::createFromFormat('d.m.Y', $offer[11])->format('Y-m-d');
		$program->entrance_start = \DateTime::createFromFormat('d.m.Y', $offer[22])->format('Y-m-d');
		$program->entrance_end = \DateTime::createFromFormat('d.m.Y', $offer[23])->format('Y-m-d');
		$program->id_school = $id_school;
		$program->min_rate = floatval($json->offers_requests_info->{$program->id_edbo}[3]); 
		$program->ave_rate = floatval($json->offers_requests_info->{$program->id_edbo}[2]);          
		$program->max_rate = floatval($json->offers_requests_info->{$program->id_edbo}[4]);
		$program->id_level = $this->getIdLevelByIdEDBO($offer[3]);
		$program->id_specialty = $this->getIdSpecialtyByIdEDBO($offer[6]);
		//$spec_edbo = $json->specialities->{$offer[6]};
		//$program->id_specialty = $this->getIdSpecialtyByIdEDBO($spec_edbo[0]);
		$program->id_form = $this->getIdFormByIdEDBO($offer[5]);
		$program->id_base = $this->getIdBaseByIdEDBO($offer[4]);
		$this->saveProgram($program);
		$program->id = $this->fetchIdEDBO($id_edbo)->id;
		return $program;
	}
	
	public function saveProgram(Program $program)
	{
		$data = [
			'id_edbo' => $program->id_edbo,
			'title' => $program->title,
			'type' => $program->type,
			'period' => $program->period,
			'year' => $program->year,
			'license_num' => $program->license_num,
			'contract_num' => $program->contract_num,
			'id_level' => $program->id_level,
			'id_specialty' => $program->id_specialty,
			'id_form' => $program->id_form,
			'id_school' => $program->id_school,
			'id_base' => $program->id_base,
			'id_faculty' => $program->id_faculty,
			'min_rate'    => $program->min_rate,
			'ave_rate'    => $program->ave_rate,
			'max_rate'    => $program->max_rate,
			'learning_start' => $program->learning_start,
			'learning_end' => $program->learning_end,
			'entrance_start' => $program->entrance_start,
			'entrance_end' => $program->entrance_end,
			'updated_at' => date('Y-m-d H:i:s'),
		];
		$id = intval($program->id);
		if ($id == 0) {
			$data['created_at'] = date('Y-m-d H:i:s');
			$this->insert($data);
		} else {
			if ($this->fetch($id)->current()) {
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
