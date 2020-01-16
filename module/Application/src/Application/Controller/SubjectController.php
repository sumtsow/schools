<?php
namespace Application\Controller;
    
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Subject;
use Application\Model\User;
use Application\Form\SubjectForm;

class SubjectController extends AbstractActionController
{
    protected $subjectTable;
	
    public function indexAction()
    {
		return new ViewModel();
    }
	
    public function addAction()
    {
		return new ViewModel();
    }
	
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin', ['action' => 'program']);
        }		
		$user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('index', ['action' => 'view', 'id' => $id]);
        }
		$locale = $this->getServiceLocator()->get('translator')->getLocale();
		$subjectForm  = new SubjectForm();
		$request = $this->getRequest();
        if ($request->isPost()) {
            $subject = new Subject();
            $subjectForm->setInputFilter($subject->getInputFilter());
            $subjectForm->setData($request->getPost());
			$result = false;
            if ($subjectForm->isValid()) {
                $subject->exchangeArray($subjectForm->getData());
                $result = $this->getSubjectTable()->save($subject);
            }
			if($result) {
				return $this->redirect()->toRoute('program', ['action' => 'edit', 'id' => $id]);					
			}
        }
		return $this->redirect()->toRoute('program', ['action' => 'edit', 'id' => $id]);
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
		/*$program = $this->getProgramTable()->fetchOne($id);
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
        );*/
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
