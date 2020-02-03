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
	
    public function indexAction()
    {
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
		$default_id = 63; // Kharkiv
		$edbo_params = $this->getServiceLocator()->get('config')['edbo'];
		$regions = $this->getRegionTable()->fetchAll();
		$request = $this->getRequest();
		$id = $this->params()->fromQuery('region');
		if (!$id) {
			$id = $default_id;
		}
		$id = sprintf("%02d", $id);
		$url = $edbo_params['api_url'] . 'universities/?ut=high&lc=' . $id . '&exp=json';
		$path = User::getDocumentRoot() . $edbo_params['local_dir'] . $id . '/';
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
		$vm = new ViewModel();
        return $vm->setVariable('regions', $regions)
			->setVariable('json', $json)
			->setVariable('id', $id)
			->setVariable('table', $this->getSchoolTable());
    }
	
    public function importAction()
    {
		$user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
		$id_edbo = $this->params()->fromRoute('id');
		if (!$id_edbo) {
            return $this->redirect()->toRoute('import');
        }
		$edbo_params = $this->getServiceLocator()->get('config')['edbo'];
		$url = $edbo_params['api_url'] . 'university/?id=' . $id_edbo . '&exp=json';
		$region_edbo = $this->params()->fromQuery('region');
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
        $school = $this->getSchoolTable()->getSchoolByIdEdbo($id_edbo);
		if(!$school) {
			$school = new School();
			$school->id = 0;
		}
		$school->id_region  = $this->getSchoolTable()->getRegionId($region_edbo);
		$json = json_decode($jsonString);
		$this->getSchoolTable()->importFromJson($school, $json);
		return $this->redirect()->toRoute('import', [], ['region' => $region_edbo]);
    }
    
    public function getProgramTable()
    {
        if (!$this->programTable) {
            $sm = $this->getServiceLocator();
			$this->programTable = $sm->get('Application\Model\ProgramTable');
        }
		return $this->programTable;
    }
	
    public function getRegionTable()
    {
        if (!$this->regionTable) {
            $sm = $this->getServiceLocator();
			$this->regionTable = $sm->get('Application\Model\RegionTable');
        }
		return $this->regionTable;
    }
	
    public function getSchoolTable()
    {
        if (!$this->schoolTable) {
            $sm = $this->getServiceLocator();
			$this->schoolTable = $sm->get('Application\Model\SchoolTable');
        }
		return $this->schoolTable;
    }

    public function getSpecialtyTable()
    {
        if (!$this->specialtyTable) {
            $sm = $this->getServiceLocator();
			$this->specialtyTable = $sm->get('Application\Model\SpecialtyTable');
        }
		return $this->specialtyTable;
    }

    public function getSubjectTable()
    {
        if (!$this->subjectTable) {
            $sm = $this->getServiceLocator();
			$this->subjectTable = $sm->get('Application\Model\SubjectTable');
        }
		return $this->subjectTable;
    }
    
}
