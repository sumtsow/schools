<?php
namespace Application\Controller;
    
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Program;
use Application\Model\User;
use Application\Form\ProgramForm;

class ProgramController extends AbstractActionController
{
	protected $programTable;	
    protected $schoolTable;
    protected $specialtyTable;
	
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
		$school = $this->getSchoolTable()->getSchool($id);
		$programForm  = new ProgramForm();
		if($school->high) {
			$id_program = $this->getProgramTable()->getProgramsByIdSchool($id);
			if($id_program) {
				$programs = $this->getProgramTable()->getPrograms($id_program);
				$programForm->get('id_specialty')->setValueOptions($this->getSpecialtyTable()->getSpecialties());
				$programForm->get('id_level')->setValueOptions($this->getProgramTable()->getLevels($locale));
				$programForm->get('id_form')->setValueOptions($this->getProgramTable()->getForms());
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
		$program = $this->getProgramTable()->fetchOne($id);
		$programForm->get('id')->setValue($program->id);
		$programForm->get('title')->setValue($program->title);
		$programForm->get('period')->setValue($program->period);
		$programForm->get('year')->setValue($program->year);
		$programForm->get('id_school')->setValue($program->id_school);
		$programForm->get('id_specialty')->setValueOptions($this->getSpecialtyTable()->getSpecialties())->setValue($program->id_specialty);
		$programForm->get('id_level')->setValueOptions($this->getProgramTable()->getLevels($locale))->setValue($program->id_level);
		$programForm->get('id_form')->setValueOptions($this->getProgramTable()->getForms())->setValue($program->id_form);
		$subjects = $this->getProgramTable()->getExamSubjects($id);
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
					'action' => 'edit', 'id' => $program->id_school
				]);					
			}
        }
        return array(
			'id' => $id,
			'programForm' => $programForm,
			'subjects' => $subjects,
        );
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
}
