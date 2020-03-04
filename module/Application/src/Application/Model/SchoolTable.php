<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;

class SchoolTable extends AbstractTableGateway
{
    protected $table ='school';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new School());

        $this->initialize();
    }
    
    public function fetchAll()
    {
        return $resultSet = $this->select();
    }
	
	public function fetch($id)
    {
        return $this->select(['id' => $id])->current();
    }
    
    public function fetchSchools($areaIndex = false, $visible = 1)
    {
        $areaIndex = $areaIndex;
        $filter = "`high` = 0";
        if($visible) {
            $filter .= " AND `visible`='1'";
        }
        if($areaIndex) {
            $areas = $this->fetchAreas();
            $currentArea = $areas[$areaIndex-1];
            $filter .= " AND `area`='$currentArea'";
        }
        $resultSet = $this->select($filter);
        foreach($resultSet as $item) {
                $result[] = $item;
        }
        return $result;
    }
    
    public function fetchUniversities($id_region = false, $sort = ['field' => 'name_uk', 'order' => 'ASC'], $type = 1, $visible = 1)
    {
        $filter = "`high` = 1";
        if($id_region) {
            $filter .= " AND `id_region`='" . $id_region . "'";
        }
		if($type) {
            $filter .= " AND `type`=" . intval($type);
        }
        if($visible) {
            $filter .= " AND `visible`='1'";
        }
        $filter .= " ORDER BY `" . $sort['field'] . "` " . $sort['order'];
        $resultSet = $this->select($filter);
		$result = [];
        foreach($resultSet as $item) {
                $result[] = $item;
        }
        return $result;
    }

    public function getSchoolByIdEdbo($id_edbo)
    {
        return $this->select(['id_edbo' => $id_edbo]);
    }

	// 'id_edbo' is a key, 'id' is a value
	public function getBothIdAsJson()
    {
        $resultSet = $this->fetchUniversities();
		$schools = '{';
		foreach($resultSet as $result) {
			$schools .= '"' . $result->id_edbo . '":"' . $result->id . '",';
		}
		$schools = rtrim($schools, ',');
		$schools .= '}';
		return $schools; 
    }

    public function fetchAreas()
    {
        $resultArr = $this->adapter->query('DESCRIBE `school` `area`', Adapter::QUERY_MODE_EXECUTE)->toArray();
        $resultArr = explode(',',str_replace("'","",substr($resultArr[0]['Type'],5,-1)));
        return $resultArr;
    }
	
    public function fetchOwnerships()
    {
        $resultArr = $this->adapter->query('DESCRIBE `school` `ownership`', Adapter::QUERY_MODE_EXECUTE)->toArray();
        $resultArr = explode(',',str_replace("'","",substr($resultArr[0]['Type'],5,-1)));
        return $resultArr;
    }
	
    public function fetchTypes()
    {
        $resultArr = $this->adapter->query('DESCRIBE `school` `type`', Adapter::QUERY_MODE_EXECUTE)->toArray();
        $resultArr = explode(',',str_replace("'","",substr($resultArr[0]['Type'],5,-1)));
		array_unshift($resultArr, null);
        return $resultArr;
    }
	
    public function getOwners()
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('owner');
        $selectString = $sql->buildSqlString($select);
		$owners = [];
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		foreach($resultSet as $result) {
			$owners[$result->id] = $result->title;
		}
        return $owners;
    }
	
     public function getRegions()
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('region');
        $selectString = $sql->buildSqlString($select);
		$regions = [];
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		foreach($resultSet as $result) {
			$regions[$result->id] = $result->title;
		}
        return $regions;
    }
	
    public function getRegionId($region_edbo)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('region')->where(['id_edbo' => $region_edbo]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current()->id;
    }
	
    public function getRegionIdEdbo($id)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('region')->where(['id' => $id]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current()->id_edbo;
    }

    public function hasJsonOffersFile($schools, $edbo_params)
    {
		$result = [];
		foreach ($schools as $school) {
			$region_edbo = $this->getRegionIdEdbo($school->id_region);
			$path = User::getDocumentRoot() . $edbo_params['local_dir'] . $region_edbo . '/'. $school->id_edbo . '/';
			$filename = $edbo_params['files']['offers'];
			if(file_exists($path . $filename)) {
				$result[$school->id] = true;
			} else {
				$result[$school->id] = false;
			}
		}
        return $result;
    }
	
    public function validateOwner($owner)
    {
		if(!$owner) { return null; }
		$sql = new Sql($this->adapter);
        $select = $sql->select()->columns(['id'])->from('owner')->where(['title' => $owner]);
        $selectString = $sql->buildSqlString($select);
		$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		$id_owner = $resultSet->current()->id;
		if(!$id_owner) {
			$insert = $sql->insert()->into('owner')->values(['title' => $owner]);
			$insertString = $sql->buildSqlString($insert);
			$this->adapter->query($insertString, $this->adapter::QUERY_MODE_EXECUTE);
			$select = $sql->select()->columns(['id'])->from('owner')->where(['title' => $owner]);
			$selectString = $sql->buildSqlString($select);
			$resultSet = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
			$id_owner = $resultSet->current()->id;
		}
        return $id_owner;
    }
	
    public function importFromJson($school, $json)
    {
		if(!$json) { return false; }
		$school->id_edbo   = $json->university_id;
		$parent = $this->getSchoolByIdEdbo($json->university_parent_id);
		$school->id_parent = $parent ? $parent->id : null;
        $school->name_uk   = $json->university_name;
		$school->name_ru   = $json->university_name;
		$school->name_en   = $json->university_name_en;
        $school->shortname = $json->university_short_name;
        $school->address   = $json->university_address . ', ' . $json->koatuu_name . ', ' . $json->region_name . ', ' . $json->post_index;
        $school->phone     = $json->university_phone;
        $school->email     = $json->university_email;
        $school->http      = $json->university_site;
        $school->info      = $json->university_edrpou;
        $school->area      = null;
        $school->high      = 1;
        $school->map       = null;
        $school->logo      = null;
        $school->visible   = 1;
		$school->type      = mb_strtolower($json->education_type_name);
        $school->ownership = mb_strtolower($json->university_financing_type_name);
        $school->id_owner  = $this->validateOwner($json->university_governance_type_name);
		$this->saveSchool($school);
		return true;
    }
	
    public function search($search = false)
    {
        $results = false;
        if($search) {
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $fields = ['id', 'name_uk', 'name_en', 'name_ru', 'shortname', 'address', 'phone', 'email', 'http', 'info'];
            $select->from($this->table)
                ->columns($fields)
                ->where->like('name_uk', '%'.$search.'%')
                ->OR->where->like('name_en', '%'.$search.'%')
                ->OR->where->like('name_ru', '%'.$search.'%')    
                ->OR->where->like('shortname', '%'.$search.'%')
                ->OR->where->like('address', '%'.$search.'%')
                ->OR->where->like('phone', '%'.$search.'%')
                ->OR->where->like('email', '%'.$search.'%')
                ->OR->where->like('http', '%'.$search.'%')
                ->OR->where->like('info', '%'.$search.'%');
            array_shift($fields);
            $statement = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($statement, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            $results = $this->found($results, $fields, $search);
        }
        return $results;
    }
    
    private function found($results, $fields, $search) {
        $found = array();
        $search = mb_strtolower($search);
        $replacement = '<span class="bg-success">'.$search.'</span>';
        foreach($results as $key => $school) {
            foreach($fields as $field) {
                $pos = mb_stripos($school[$field], $search);
                if(false !== $pos) {
                    $results[$key]['found'][] = [
                        'field' => $field,
                        'value' => str_ireplace($search, $replacement, mb_strtolower($school[$field])),
                    ];
                }
            }
        }
        return $results;
    }
	
    public function syncSchool($id, $edbo_params)
    {
		$school = $this->getSchool($id);
		$university_name = urlencode($school->name_uk);
		$params1 = [
			'action' => 'universities',
			'university-id' => '',
			'qualification' => '1',
			'education-base' => '40',
			'speciality' => '121',
			'program' => '',
			'education-form' => '',
			'course' => '',
			'region' => '',
			'university-name' => '',
		];
		$params2 = [
			'action' => 'universities',
			'university-id' => '',
			'qualification' => '1',
			'education-base' => '40',
			'speciality' => '123',
			'program' => '',
			'education-form' => '',
			'course' => '',
			'region' => '',
			'university-name' => $school->name_uk,
		];		
		$data = http_build_query($params2);
		$host = parse_url($edbo_params['url'])['host'];
		$opts = [
			'http' => [
				'method' => 'post',
				'header'=> //'Host: ' . $host . '\r\n',
							//'Accept: application/json, text/javascript, */*; q=0.01\r\n' .
							//'Origin: ' . $edbo_params['url'] . '\r\n',
							//'Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n' .
							'Referer: ' . $edbo_params['url'],
				'content' => $data,
				//'options' => '{language: json}'
			]
		];
		$context = stream_context_create($opts);
		$file = file_get_contents($edbo_params['url'], false, $context);
		//$dom = new \DomDocument('1.0', 'utf-8');
		//$dom->loadHTML($file);
        return $file;
    }
	
    public function saveSchool(School $school)
    {
        $school->map = stripslashes($school->map);
        $data = array(
			'id_edbo' => $school->id_edbo,
			'id_region' => $school->id_region,
			'id_parent' => $school->id_parent,
            'name_uk' => $school->name_uk,
            'name_en' => $school->name_en,
            'name_ru' => $school->name_ru,
            'shortname' => $school->shortname,
            'address'  => $school->address,
            'phone' => $school->phone,
            'email'  => $school->email,			
            'http' => $school->http,
            'info'  => $school->info,
            'area'  => $school->area,
            'high' => $school->high,
            'map'  => $school->map,
            'logo'  => $school->logo,
            'visible'  => $school->visible,
            'type'  => $school->type,
            'ownership'  => $school->ownership,
            'id_owner'  => $school->id_owner,
        );
        $data['area']++; 
        $id = $school->id;

        if ($id === 0) {
            return $this->insert($data);
        } elseif ($this->fetch($id)) {
            return $this->update($data, ['id' => $id]);
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function deleteSchool($id)
    {
        $this->delete(['id' => $id]);
    }
}
