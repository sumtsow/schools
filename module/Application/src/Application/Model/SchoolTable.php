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
        return $this->select(['id' => $id]);
    }
    
    public function fetchSchools($areaIndex = null, $visible = 1)
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
    
    public function fetchUniversities($visible = 1)
    {
        $filter = "`high` = 1";
        if($visible) {
            $filter .= " AND `visible`='1'";
        }
        $resultSet = $this->select($filter);
        foreach($resultSet as $item) {
                $result[] = $item;
        }
        return $result;
    }
    
     public function fetchAreas()
    {
        $resultSet = $this->adapter->query('DESCRIBE `school` `area`', Adapter::QUERY_MODE_EXECUTE);
        $resultArr = $resultSet->toArray();
        $resultArr = explode(',',str_replace("'","",substr($resultArr[0]['Type'],5,-1)));
        return $resultArr;
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
    
    public function getSchool($id)
    {
        $rowset = $this->select(array(
            'id' => $id,
        ));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
		
	public function getProgramsId($id_school)
    {
		$sql = new Sql($this->adapter);
        $select = $sql->select()->from('school_has_program')->columns(['id_program'])->where(['id_school' => $id_school]);
        $selectString = $sql->buildSqlString($select);
        $programs = $this->adapter->query($selectString, $this->adapter::QUERY_MODE_EXECUTE);
		$ids = [];
		foreach($programs as $program) {
			$ids[] = $program['id_program'];
		}
		return (count($ids)) ? $ids : false;
    }

    public function saveSchool(School $school)
    {
        $school->map = stripslashes($school->map);
        $data = array(
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
        );
        $data['area']++; 
        $id = $school->id;

        if ($id === 0) {
            $this->insert($data);
        } elseif ($this->getSchool($id)) {
            $this->update(
                $data,
                array(
                    'id' => $id,
                )
            );
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function deleteSchool($id)
    {
        $this->delete(array(
            'id' => $id,
        ));
    }
}
