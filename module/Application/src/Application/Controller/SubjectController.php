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
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin', ['action' => 'program']);
        }		
		$user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('index', ['action' => 'view', 'id' => $id]);
        }
        $request = $this->getRequest();
        $vm = new ViewModel();
		return $vm->setVariable('data',  $request->getPost());
    }
	
    public function addAction()
    {
        $id_program = (int) $this->params()->fromRoute('id', 0);
        if (!$id_program) {
            return $this->redirect()->toRoute('admin', ['action' => 'program']);
        }		
		$user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('index', ['action' => 'view', 'id' => $id_program]);
        }
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
            } else {
                $error_input = ['field' => key($subjectForm->getMessages())];
            }
        }
		return $this->redirect()->toRoute('program', ['action' => 'edit', 'id' => $id_program], ['error_input' => $error_input]);
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
		$subjectForm  = new SubjectForm();
		$request = $this->getRequest();
		$output = null;
        if ($request->isPost()) {
            $subject = new Subject();
            $subjectForm->setInputFilter($subject->getInputFilter());
			$checkbox = $request->getPost('required');
            $subjectForm->setData($request->getPost());
			$result = false;
            if ($subjectForm->isValid()) {
                $subject->exchangeArray($subjectForm->getData());
                $result = $this->getSubjectTable()->save($subject);
            } else {
				$output = [
					'error_input' => ['field' => key($subjectForm->getMessages())],
					'errors' => $subjectForm->getMessages()
				];
            }
        }
		return $this->redirect()->toRoute('program', ['action' => 'edit', 'id' => $id], $output);
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
				'action' => 'edit', 'id' => '1'
			]);
		}
		$subject = $this->getSubjectTable()->fetch($id);
		$request = $this->getRequest();
		if ($request->isPost()) {
			$del = $request->getPost('del', 'No');
			if ($del == 'Yes') {
			    $post_id = (int) $request->getPost('id');
				$this->getSubjectTable()->deleteSubject($post_id);
			}
			return $this->redirect()->toRoute('program', [
				'action' => 'edit', 'id' => $id
			]);	
		};
		$vm = new ViewModel();
        return $vm->setVariable('id_program',  $subject->id_program)->setVariable('id',  $id);
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
