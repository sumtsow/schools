<?php
namespace Application\Controller;
    
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Program;
use Application\Model\Subject;
use Application\Model\User;
use Application\Form\ProgramForm;
use Application\Form\SubjectForm;

class ProgramController extends AbstractActionController
{
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
        return new ViewModel();
    }
	
    public function addAction()
    {
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin', ['action' => 'index']);
        }
		$locale = $this->getServiceLocator()->get('translator')->getLocale();
		$school = $this->getSchoolTable()->fetchOne($id);
		$programForm  = new ProgramForm();
		if($school->high) {
			$id_program = $this->getProgramTable()->getProgramsByIdSchool($id);
			if($id_program) {
				$programs = $this->getProgramTable()->getPrograms($id_program);
				$programForm->get('id_specialty')->setValueOptions($this->getSpecialtyTable()->getSpecialties());
				$programForm->get('id_level')->setValueOptions($this->getProgramTable()->getLevels($locale));
				$programForm->get('id_form')->setValueOptions($this->getProgramTable()->getForms());
				$programForm->get('id_base')->setValueOptions($this->getProgramTable()->getBases());
			}
		}
		$request = $this->getRequest();
        if ($request->isPost()) {
            $program = new Program();
            $programForm->setInputFilter($program->getInputFilter());
            $programForm->setData($request->getPost());
			$result = false;
            if ($programForm->isValid()) {
                $program->exchangeArray($programForm->getData());
                $result = $this->getProgramTable()->saveProgram($program);
            }
			if($result) {
				return $this->redirect()->toRoute('admin', [
					'action' => 'edit', 'id' => $id
				]);					
			} else {
				return $this->redirect()->toRoute('program', [
					'action' => 'add', 'id' => $id
				]);			
			}
        }
        return array(
			'id' => $id,
			'programForm' => $programForm,
            'programs' => $programs,
        );
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
            return $this->redirect()->toRoute('admin', ['action' => 'index']);
        }
		$locale = $this->getServiceLocator()->get('translator')->getLocale();
		$programForm  = new ProgramForm();
		$program = $this->getProgramTable()->fetch($id)->current();
		$school = $this->getSchoolTable()->fetchOne($program->id_school);		
		$programForm->bind($program);
		$programForm->get('id_specialty')->setValueOptions($this->getSpecialtyTable()->getSpecialties())->setValue($program->id_specialty);
		$programForm->get('id_level')->setValueOptions($this->getProgramTable()->getLevels($locale))->setValue($program->id_level);
		$programForm->get('id_form')->setValueOptions($this->getProgramTable()->getForms())->setValue($program->id_form);
		$programForm->get('id_base')->setValueOptions($this->getProgramTable()->getBases())->setValue($program->id_base);
		$types = $this->getProgramTable()->getTypes();
		$programForm->get('type')->setValueOptions($types)->setValue(array_search($program->type, $types));
		$subjects = $this->getProgramTable()->getExamSubjects($id);
		$allSubjects = $this->getSubjectTable()->fetchAll();
		$request = $this->getRequest();
		$vm = new ViewModel();
        if ($request->isPost()) {
            $program = new Program();
            $programForm->setInputFilter($program->getInputFilter());
            $programForm->setData($request->getPost());
			$result = false;
            if ($programForm->isValid()) {
                $program->exchangeArray($programForm->getData());
                $result = $this->getProgramTable()->saveProgram($program);
			    if($result) {
				    return $this->redirect()->toRoute('program', [
					    'action' => 'edit', 'id' => $id
				    ]);					
			    }                
            } else {
                $vm->setVariable('error_input', [
                    'field' => key($programForm->getMessages()),
                ]);
            }
        }
        $subjectsForm = new SubjectForm();
        $subject = new Subject();
        $subjectsForm->bind($subject);
		$subjectsForm->get('id_subject')->setValueOptions($this->getSubjectTable()->getSubjects());
		$addSubjectForm = new SubjectForm();
        $addSubject = new Subject();
        $addSubjectForm->bind($addSubject);		
        $addSubjectForm->get('id_subject')->setValueOptions($this->getSubjectTable()->getSubjects());
        return $vm->setVariable('id', $id)
            ->setVariable('school', $school)
            ->setVariable('programForm', $programForm)
            ->setVariable('subjects', $subjects)
            ->setVariable('allSubjects', $allSubjects)
            ->setVariable('subjectsForm', $subjectsForm)
			->setVariable('addSubjectForm', $addSubjectForm);
    }

	public function deleteAction() {
		
		$user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
			return $this->redirect()->toRoute('admin', [
				'action' => 'edit', 'id' => $id
			]);
		}
		$program = $this->getProgramTable()->fetchOne($id);
		$request = $this->getRequest();
		if ($request->isPost()) {
			$del = $request->getPost('del', 'No');
			if ($del == 'Yes') {
				$id = (int) $request->getPost('id');
				$this->getProgramTable()->deleteProgram($id);
			}
			return $this->redirect()->toRoute('admin', [
				'action' => 'edit', 'id' => $program->id_school
			]);			
		}
        return array(
            'program' => $program,
        );
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
}
