<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

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
    
    public function fetchSchools($areaIndex = null, $visible = 1)
    {
        $areaIndex = (int) $areaIndex;
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
    
    public function getSchool($id)
    {
        $id  = (int) $id;

        $rowset = $this->select(array(
            'id' => $id,
        ));

        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    public function saveSchool(School $school)
    {
        $school->map = stripslashes($school->map);
		$data = array(
            'name' => $school->name,
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
        $id = (int) $school->id;

        if ($id == 0) {
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
