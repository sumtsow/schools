<?php
namespace Application\Controller;
    
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\User;
use Application\Model\School;

class ImportController extends AbstractActionController
{
	protected $programTable;
	protected $regionTable;
    protected $schoolTable;
    protected $specialtyTable;
	protected $subjectTable;
	
    public function indexAction() {
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
		$default_id = 20; // Kharkiv
		$edbo_params = $this->getServiceLocator()->get('config')['edbo'];
		$regions = $this->getRegionTable()->fetchAll();
		$request = $this->getRequest();
		$id = $this->params()->fromQuery('region');
		if (!$id) {
			$id = $default_id;
		}
		$region_edbo = $this->getRegionTable()->getIdEdbo($id);
		$url = $edbo_params['api_url'] . 'universities/?ut=high&lc=' . $region_edbo . '&exp=json';
		$path = User::getDocumentRoot() . $edbo_params['local_dir'] . $region_edbo . '/';
		if(!file_exists($path)) {
			mkdir($path);
		}
		$filename = $edbo_params['files']['universities'];
		// if local file exists take local
		if(file_exists($path . $filename)) {
			$text = file_get_contents($path . $filename);
		} else {
			$text = file_get_contents($url);
			if($text) {
				file_put_contents($path . $filename, $text);
			}
		}
		$json = json_decode($text);
		$schoolsString = $this->getSchoolTable()->getBothIdAsJson();
		//$text = json_encode($json);
		$vm = new ViewModel();
        return $vm->setVariable('regions', $regions)
			->setVariable('json', $json)
			->setVariable('jsonString', $text)
			->setVariable('schoolsString', $schoolsString)
			->setVariable('id', $id)
			->setVariable('table', $this->getSchoolTable());
    }
	
    public function importAction() {
		$user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
		$id_region = $this->params()->fromQuery('region');
		$region_edbo = $this->getRegionTable()->getIdEdbo($id_region);
		$import_all = $this->params()->fromQuery('all');
		$id_edbo = $this->params()->fromRoute('id');
		$request = $this->getRequest();
		if ($id_edbo) {
			// single import
			$this->importSchool($id_edbo, $region_edbo);
		} elseif($request->isPost()) {
			if($import_all) {
				// all import
				$jsonString = $request->getPost('id_university');
				$json = json_decode($jsonString);
				foreach($json as $school) {
					$this->importSchool($school->university_id, $region_edbo);
				}
			} else {
				$id_edbo_Array = $request->getPost('id_university');
				if(is_array($id_edbo_Array)) {
					// Array import
					foreach($id_edbo_Array as $id => $value) {
						$this->importSchool($id, $region_edbo);
					}
				}
			}
        }
		return $this->redirect()->toRoute('import', [], ['query' => ['region' => $id_region]]);
    }

	public function importSchool($id_edbo, $region_edbo) {
		$edbo_params = $this->getServiceLocator()->get('config')['edbo'];
		$url = $edbo_params['api_url'] . 'university/?id=' . $id_edbo . '&exp=json';
		$path = User::getDocumentRoot() . $edbo_params['local_dir'] . $region_edbo . '/' . $id_edbo . '/';
		if(!file_exists($path)) {
			mkdir($path);
		}
		$filename = $edbo_params['files']['universities'];
		// if local file exists take local
		if(file_exists($path . $filename)) {
			$jsonString = file_get_contents($path . $filename);
		} else {
			$jsonString = file_get_contents($url);
			if($jsonString) {
				file_put_contents($path . $filename, $jsonString);
			}
		}
		$json = json_decode($jsonString);
	    $school = $this->getSchoolTable()->getSchoolByIdEdbo($id_edbo);
		if(!$school) {
			$school = new School();
			$school->id = 0;
		}
		$school->id_region = $this->getSchoolTable()->getRegionId($region_edbo);
		$this->getSchoolTable()->importFromJson($school, $json);	
	}

    public function getProgramTable() {
        if (!$this->programTable) {
            $sm = $this->getServiceLocator();
			$this->programTable = $sm->get('Application\Model\ProgramTable');
        }
		return $this->programTable;
    }
	
    public function getRegionTable() {
        if (!$this->regionTable) {
            $sm = $this->getServiceLocator();
			$this->regionTable = $sm->get('Application\Model\RegionTable');
        }
		return $this->regionTable;
    }
	
    public function getSchoolTable() {
        if (!$this->schoolTable) {
            $sm = $this->getServiceLocator();
			$this->schoolTable = $sm->get('Application\Model\SchoolTable');
        }
		return $this->schoolTable;
    }

    public function getSpecialtyTable() {
        if (!$this->specialtyTable) {
            $sm = $this->getServiceLocator();
			$this->specialtyTable = $sm->get('Application\Model\SpecialtyTable');
        }
		return $this->specialtyTable;
    }

    public function getSubjectTable() {
        if (!$this->subjectTable) {
            $sm = $this->getServiceLocator();
			$this->subjectTable = $sm->get('Application\Model\SubjectTable');
        }
		return $this->subjectTable;
    }
    
}
