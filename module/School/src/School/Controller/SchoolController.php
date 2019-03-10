<?php
namespace School\Controller;

/*if(isset($_COOKIE['PHPSESSID'])) {
    $sid = $_COOKIE['PHPSESSID'];
}
else {
    $sid = session_id();
}

if ($sid!=='') {
    session_id($sid);
}*/

session_start();
setcookie('PHPSESSID',session_id(),0,'/');
    
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Config\Reader\Xml as XmlReader;
use Zend\Config\Writer\Xml as XmlWriter;
use Zend\Feed\Reader\Reader;
use School\Model\School;
use School\Form\SchoolForm;
use School\Model\Comment;
use School\Form\CommentForm;
use School\Model\User;
use School\Form\UserForm;
use School\Model\Rss;

class SchoolController extends AbstractActionController
{
    protected $schoolTable;
    protected $commentTable;
	
    public function indexAction()
    {
        $reader = new XmlReader();
        $confArray = $this->getServiceLocator()->get('config');
        $news = new Rss($this->request->getUri()->getScheme().'://'.$this->request->getUri()->getHost().'/'.$confArray['rss']['file']);
        $area = ($this->request->getPost('area')) ? $this->request->getPost('area') : $this->params()->fromRoute('area', 0);
        $result = $this->getSchoolTable()->fetchSchools($this->params()->fromRoute('id', 0), $area);
        $paginator = new Paginator(new ArrayAdapter($result));
        $page = ($this->request->getPost('area')) ? 0 : $this->params()->fromRoute('page');            
        $paginator->setCurrentPageNumber($page)    
            ->setItemCountPerPage($confArray['per_page'])
            ->setPageRange(ceil(count($result)/$confArray['per_page']));
        $user = new User();
        $vm = new ViewModel();
        return $vm->setVariable('paginator', $paginator)
            ->setVariable('areas', $this->getSchoolTable()->fetchAreas())
            ->setVariable('high', $this->params()->fromRoute('id', 0))
            ->setVariable('area', $area)
            ->setVariable('news', $news)
            ->setVariable('news_max', $confArray['rss']['max'])
            ->setVariable('username', ($user->isValid()) ? $user->__get('login') : '');
    }
	
    public function schoolsAction()
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
	
    public function viewAction()
    {
	$id = (int) $this->params()->fromRoute('id', 0);
        $area = $this->params()->fromRoute('area', 0);
        $vm = new ViewModel();
        $user = new User();        
	$vm->setVariable('school',$this->getSchoolTable()->getSchool($id))
            ->setVariable('comments',$this->getCommentTable()->fetchComments($id))
            ->setVariable('docRoot',User::getDocumentRoot())
            ->setVariable('username', ($user->isValid()) ? $user->__get('login') : null);
        $form = new CommentForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $comment = $this->getCommentTable()->getComment($request->getPost('id'));
            $form->setInputFilter($comment->getInputFilter()); 
            $formData = $request->getPost();
            $formData['id_school'] = $id;
            $form->setData($formData);
            if ($form->isValid()) {
                $comment->exchangeArray($form->getData());
                $this->getCommentTable()->saveComment($comment);
                return $this->redirect()->toRoute('school', array(
                    'action' => 'view', 'id' => $id
                ));
            }
        }
        return $vm->setVariable('form',$form);
    }
	
    public function addAction()
    {
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('school', array(
                'action' => 'schools'
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
                return $this->redirect()->toRoute('school', array(
                    'action' => 'schools', 'id'=> $school->high
                ));
            }
        }
        return array('form' => $form);

    }

    public function editAction()
    {
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('school', array(
                'action' => 'schools'
            ));
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('school', array(
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
                return $this->redirect()->toRoute('school', array(
                    'action' => 'schools', 'id'=> $school->high
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
            return $this->redirect()->toRoute('school', array(
                'action' => 'schools'
            ));
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('school', array(
                'action' => 'schools'
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
            return $this->redirect()->toRoute('school', array(
                'action' => 'schools', 'id'=> $high
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
            return $this->redirect()->toRoute('school', array(
                'action' => 'schools'
            ));            
        }
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('school', array(
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
            return $this->redirect()->toRoute('school', array(
                'action' => 'view',
                'id'=> $comment->id_school
            ));            
        }
        return array(
            'id'    => $id,
            'comment' => $this->getCommentTable()->getComment($id)
        );
    }

    public function loginAction()
    {
        $form  = new UserForm();
        $request = $this->getRequest();
        $user = new User();    
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $result = $user->login($request->getPost('login'), $request->getPost('passwd'), User::getDocumentRoot().'/admin');
                return $this->redirect()->toRoute('school');
            }
        }
    
    return array(
        'form' => $form,
        'username' => $user->__get('login'),
    );
    
    }
    
    public function logoutAction()
    {
        $user = new User();
        $user->logout();
        return $this->redirect()->toRoute('school');
    }    
	
    public function getSchoolTable()
    {
        if (!$this->schoolTable) {
            $sm = $this->getServiceLocator();
        }
    return $sm->get('School\Model\SchoolTable');
    }
        
    public function getCommentTable()
    {
        if (!$this->commentTable) {
            $sm = $this->getServiceLocator();
        }
        return $sm->get('School\Model\CommentTable');
    }
        
    public function updatenewsAction()
    {
        $confRow = $this->getServiceLocator()->get('config')['rss'];
        file_put_contents(User::getDocumentRoot().'/'.$confRow['file'], file_get_contents($confRow['url'].'/'.$confRow['file']));
    return $this->redirect()->toRoute('school');
    }        
}
