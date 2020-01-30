<?php
namespace Application\Controller;
    
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\User;


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
		$id = $this->request->getPost('region');
		$id = $id ? $id : $default_id;
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
        $schools = $this->getSchoolTable()->fetchUniversities();		
		$vm = new ViewModel();
        return $vm->setVariable('regions', $regions)
			->setVariable('json', $json)
			->setVariable('schools', $schools)	
			->setVariable('id', $id);
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
