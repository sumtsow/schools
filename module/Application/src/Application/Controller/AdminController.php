<?php
namespace Application\Controller;
    
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Application\Model\School;
use Application\Form\SchoolForm;
use Application\Form\CommentForm;

class AdminController extends AbstractActionController
{
    protected $schoolTable;
    protected $commentTable;
	
    public function indexAction()
    {
        $confArray = $this->getServiceLocator()->get('config');
        $area = ($this->request->getPost('area')) ? $this->request->getPost('area') : $this->params()->fromRoute('area', 0);
        $result = $this->getSchoolTable()->fetchSchools($this->params()->fromRoute('id', 0), $area);
            $paginator = new Paginator(new ArrayAdapter($result));
            $paginator->setCurrentPageNumber(
                    ($this->request->getPost('area')) ? 0 : $this->params()->fromRoute('page')
                )
                ->setItemCountPerPage($confArray['per_page'])
                ->setPageRange(ceil(count($result)/$confArray['per_page']));
            $user = new User();
            $vm = new ViewModel();
            return $vm->setVariable('paginator', $paginator)
                ->setVariable('areas', $this->getSchoolTable()->fetchAreas())
                ->setVariable('high', $this->params()->fromRoute('id', 0))
                ->setVariable('area', $area)
                ->setVariable('username', ($user->isValid()) ? $user->__get('login') : null);
    }
	
    public function addAction()
    {
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
        $form = new SchoolForm();
        $form->get('submit')->setValue('Добавить')
            ->get('area')->setValueOptions($this->getSchoolTable()->fetchAreas())
            ->get('high')->setChecked($this->params()->fromRoute('id', 0));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $school = new School();
            $form->setInputFilter($school->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $school->exchangeArray($form->getData());
                $this->getSchoolTable()->saveSchool($school);
                return $this->redirect()->toRoute('schools', array(
                    'action' => 'index', 'id'=> $school->high
                ));
            }
        }
        return array('form' => $form);

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
            return $this->redirect()->toRoute('schools', array(
                'action' => 'add'
            ));
        }
        $school = $this->getSchoolTable()->getSchool($id);
        $form  = new SchoolForm();
        $form->bind($school);
        $form->get('area')->setValueOptions($this->getSchoolTable()->fetchAreas());
        $form->get('area')->setValue(array_search($school->area,$form->get('area')->getValueOptions()));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($school->getInputFilter())
                ->setData($request->getPost());
            if ($form->isValid()) {
                $this->getSchoolTable()->saveSchool($form->getData());
                return $this->redirect()->toRoute('schools', array(
                    'action' => 'index', 'id'=> $school->high
                ));
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
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
            return $this->redirect()->toRoute('schools', array(
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
