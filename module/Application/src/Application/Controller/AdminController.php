<?php
namespace Application\Controller;
    
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Application\Model\Program;
use Application\Model\School;
use Application\Model\Subject;
use Application\Model\User;
use Application\Form\SchoolForm;
use Application\Form\ProgramForm;

class AdminController extends AbstractActionController
{
    protected $commentTable;
	protected $programTable;	
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
        $confArray = $this->getServiceLocator()->get('config');
        $area = ($this->request->getPost('area')) ? $this->request->getPost('area') : $this->params()->fromRoute('area', 0);
        $id = ($this->params()->fromRoute('id')) ? $this->params()->fromRoute('id') : 0;
        $result = ($id == 1) ? $this->getSchoolTable()->fetchUniversities(0) : $this->getSchoolTable()->fetchSchools($area, 0);
        $paginator = new Paginator(new ArrayAdapter($result));
        $page = ($this->request->getPost('area')) ? 0 : $this->params()->fromRoute('page');  
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($confArray['per_page'])
            ->setPageRange(ceil(count($result)/$confArray['per_page']));
        $vm = new ViewModel();
		$this->layout()->setVariable('high', $id);
        return $vm->setVariable('paginator', $paginator)
            ->setVariable('areas', $this->getSchoolTable()->fetchAreas())
            ->setVariable('high', $this->params()->fromRoute('id', 0))
            ->setVariable('area', $area)
            ->setVariable('username', ($user->isValid()) ? $user->getLogin() : null)
			->setVariable('edbo_params', $this->getServiceLocator()->get('config')['edbo']);
    }
	
    public function addAction()
    {
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
        $schoolForm = new SchoolForm();
        $schoolForm->get('area')->setValueOptions($this->getSchoolTable()->fetchAreas());
        $schoolForm->get('high')->setChecked($this->params()->fromRoute('id', 0));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $school = new School();
            $schoolForm->setInputFilter($school->getInputFilter());
            $schoolForm->setData($request->getPost());

            if ($schoolForm->isValid()) {
                $school->exchangeArray($schoolForm->getData());
                $this->getSchoolTable()->saveSchool($school);
                return $this->redirect()->toRoute('schools', array(
                    'action' => 'index', 'id'=> $school->high
                ));
            }
        }
        return array('form' => $schoolForm);

    }
	
    public function editAction()
    {
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin', array(
                'action' => 'add'
            ));
        }
		$locale = $this->getServiceLocator()->get('translator')->getLocale();
        $school = $this->getSchoolTable()->getSchool($id);
        $schoolForm  = new SchoolForm();
        $schoolForm->bind($school);
        $schoolForm->get('area')->setValueOptions($this->getSchoolTable()->fetchAreas());
		$schoolForm->get('id_region')->setValueOptions($this->getSchoolTable()->getRegions());
		$schoolForm->get('id_region')->setValue($school->id_region);
		$programForm  = new ProgramForm();
		$programs = false;
		$specialtyDOM = false;
		if($school->high) {
			$id_program = $this->getProgramTable()->getProgramsByIdSchool($id);
			if($id_program) {
				$programs = $this->getProgramTable()->getPrograms($id_program);
				if($programs) {
				    $programForm->get('id_specialty')->setValueOptions($this->getSpecialtyTable()->getSpecialties());
				    $programForm->get('id_level')->setValueOptions($this->getProgramTable()->getLevels($locale));
				    $programForm->get('id_form')->setValueOptions($this->getProgramTable()->getForms($locale));
					$specialtyDOM = $this->getProgramTable()->getSpecialtyDOM($id, $locale);
			    }
			}
		}
        $schoolForm->get('area')->setValue(array_search($school->area, $schoolForm->get('area')->getValueOptions()));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $schoolForm->setInputFilter($school->getInputFilter())
                ->setData($request->getPost());
            if ($schoolForm->isValid()) {
                $this->getSchoolTable()->saveSchool($schoolForm->getData());
                return $this->redirect()->toRoute('admin', array(
                    'action' => 'index', 'id'=> $school->high
                ));
            }
        }
        return array(
            'id' => $id,
            'schoolForm' => $schoolForm,
			'programForm' => $programForm,
            'programs' => $programs,
            'specialtyDOM' => $specialtyDOM,
        );
    }
	
    public function updateAction()
    {
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
		$id = (int) $this->params()->fromRoute('id', 0);
		$edbo_params = $this->getServiceLocator()->get('config')['edbo'];
		$school = $this->getSchoolTable()->getSchool($id);
		if($school->high) {
			$default_id = 63; // Kharkiv
			$path = User::getDocumentRoot() . $edbo_params['local_dir'] . $default_id . '/'. $school->id_edbo . '/';			
			/*$filename = $edbo_params['files']['universities'];
			$text = file_get_contents($path . $filename);
			$jsonUniversity = json_decode($text)->universities[0];
			$id_offers = explode(',', $jsonUniversity[3]);*/
			$filename = $edbo_params['files']['offers'];
			$text = file_get_contents($path . $filename);
			$jsonOffers = json_decode($text);
			foreach($jsonOffers->offers as $key => $offer) {
				$program = $this->getProgramTable()->importProgramFromJson($id, $offer[0], $jsonOffers, $key);
				$program->subjects[$program->id] = $this->getSubjectTable()->importSubjectFromJson($program->id, $program->id_edbo, $jsonOffers->offers_subjects);
			}
		}
		/*$response = $this->getResponse();
		$response->setStatusCode(200)->setContent($text);
		return $response;*/
		return ['data' => $program];
	}
	
    public function deleteAction()
    {
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del');
            $school = $this->getSchoolTable()->getSchool($id);
            $high = $school->high;
            if ($del == 'Да') {
                $id = (int) $request->getPost('id');
                $this->getSchoolTable()->deleteSchool($id);
            }
            // Redirect to list of schools
            return $this->redirect()->toRoute('admin', array(
                'action' => 'index', 'id'=> $high
            ));            
        }
        return array(
            'id'    => $id,
            'school' => $this->getSchoolTable()->getSchool($id)
        );
    }
    
    public function delcommentAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'schools'
            ));            
        }
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'view', 'id' => 0
            ));
        }
        $request = $this->getRequest();
        if ($request->isPost('delComment')) {
            $comment = $this->getCommentTable()->getComment($id);
            if ($request->getPost('delComment')) {
                $this->getCommentTable()->deleteComment($id);
            }
            // Redirect to list of schools
            return $this->redirect()->toRoute('schools', array(
                'action' => 'view',
                'id'=> $comment->id_school
            ));            
        }
        return array(
            'id'    => $id,
            'comment' => $this->getCommentTable()->getComment($id)
        );
    }
        
    public function getCommentTable()
    {
        if (!$this->commentTable) {
            $sm = $this->getServiceLocator();
			$this->commentTable = $sm->get('Application\Model\CommentTable');
        }
        return $this->commentTable;
    } 
	
    public function getProgramTable()
    {
        if (!$this->programTable) {
            $sm = $this->getServiceLocator();
			$this->programTable = $sm->get('Application\Model\ProgramTable');
        }
		return $this->programTable;
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
	
    public function updatenewsAction()
    {
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
        $confRow = $this->getServiceLocator()->get('config')['rss'];
        file_put_contents(User::getDocumentRoot().'/'.$confRow['file'], file_get_contents($confRow['url'].'/'.$confRow['file']));
		return $this->redirect()->toRoute('schools');
    }        
}
