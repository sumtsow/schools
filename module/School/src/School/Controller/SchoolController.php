<?php
namespace School\Controller;

if(isset($_COOKIE['PHPSESSID'])) {
    $sid = $_COOKIE['PHPSESSID'];
}
else {
    $sid = session_id();
}

if ($sid!=='') {
    session_id($sid);
}

session_start();
setcookie('PHPSESSID',session_id(),0,'/');
    
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Config\Reader\Xml as XmlReader;
use Zend\Config\Writer\Xml as XmlWriter;
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
            $per_page = $confArray['per_page'];            
            $confRow = $confArray['rss'];
            $file = $confRow['file'];
            $news_max = $confRow['max'];
            $news = $reader->fromFile(User::getDocumentRoot().'/'.$file);
            $user = new User();
            $username = '';
            $high = $this->params()->fromRoute('id', 0); 
            $vm = new ViewModel();
            $areas = $this->getSchoolTable()->fetchAreas();
            $area = $this->params()->fromRoute('area', 0);
            $newArea = $this->request->getPost('area');
            if(isset($newArea)) {
                $area = $newArea;
            }
            $result = $this->getSchoolTable()->fetchSchools($high,$area);
            $adapter = new ArrayAdapter($result);
            $paginator = new Paginator($adapter);
            $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
            if(isset($newArea)) {
                $paginator->setCurrentPageNumber(0);
            }        
            $paginator->setItemCountPerPage($per_page);
            $paginator->setPageRange(ceil(count($result)/$per_page));
            $vm->setVariable('paginator', $paginator);
            $vm->setVariable('areas', $areas);
            $vm->setVariable('high', (int) $high);
            $vm->setVariable('area', $area);            
            if ($user->isValid()) {
                $username = $user->__get('login');
            }
            return new ViewModel(array(
                'news' => $news,
                'news_max' => $news_max,
                'username' => $username,
                
        ));
    }
	
    public function schoolsAction()
    {
            $high = $this->params()->fromRoute('id', 0);
            $confArray = $this->getServiceLocator()->get('config');
            $per_page = $confArray['per_page'];
            $vm = new ViewModel();
            $areas = $this->getSchoolTable()->fetchAreas();
            $area = $this->params()->fromRoute('area', 0);
            $newArea = $this->request->getPost('area');
            if(isset($newArea)) {
                $area = $newArea;
            }
            $result = $this->getSchoolTable()->fetchSchools($high,$area);
            $adapter = new ArrayAdapter($result);
            $paginator = new Paginator($adapter);
            $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
            if(isset($newArea)) {
                $paginator->setCurrentPageNumber(0);
            }        
            $paginator->setItemCountPerPage($per_page);
            $paginator->setPageRange(ceil(count($result)/$per_page));
            $vm->setVariable('paginator', $paginator);
            $vm->setVariable('areas', $areas);
            $vm->setVariable('high', (int) $high);
            $vm->setVariable('area', $area);
            $user = new User();
            if ($user->isValid()) {
                $vm->setVariable('username', $user->__get('login'));
            }
            return $vm;
    }
	
    public function viewAction()
    {
	$id = (int) $this->params()->fromRoute('id', 0);
        $area = $this->params()->fromRoute('area', 0);
        $vm = new ViewModel();
        $user = new User();        
        if ($user->isValid()) {
            $vm->setVariable('username',$user->__get('login'));
        }        
	$vm->setVariable('school',$this->getSchoolTable()->getSchool($id));
        $vm->setVariable('comments',$this->getCommentTable()->fetchComments($id));
        $vm->setVariable('docRoot',User::getDocumentRoot());
        $form = new CommentForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id_comment = $request->getPost('id');
            $comment = $this->getCommentTable()->getComment($id_comment);
            $filter = $comment->getInputFilter();
            $form->setInputFilter($filter); 
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
        $vm->setVariable('form',$form);
        return $vm;
    }
	
    public function addAction()
    {
        $high = (int) $this->params()->fromRoute('id', 0);
        $user = new User();
        if (!$user->isValid()) {
            return $this->redirect()->toRoute('school', array(
                'action' => 'schools'
            ));
        }
        $form = new SchoolForm();
        $form->get('submit')->setValue('Добавить');
        $form->get('area')->setValueOptions($this->getSchoolTable()->fetchAreas());
        $form->get('high')->setChecked($high);
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
        $areaId = array_search($school->area,$form->get('area')->getValueOptions());
        $form->get('area')->setValue($areaId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($school->getInputFilter());
            $form->setData($request->getPost());
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
            $del = $request->getPost('delComment');
            $comment = $this->getCommentTable()->getComment($id);
            $id_school = $comment->id_school;            
            if ($del == 'Delete') {
                $this->getCommentTable()->deleteComment($id);
            }
            // Redirect to list of schools
            return $this->redirect()->toRoute('school', array(
                'action'=>'view',
                'id'=>$id_school
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
    $form->get('submit')->setAttribute('value', 'Ok');
    $request = $this->getRequest();
    $user = new User();    
    if ($request->isPost()) {
        $form->setInputFilter($user->getInputFilter());
        $form->setData($request->getPost());
        if ($form->isValid()) {
            $path = User::getDocumentRoot().'/admin';
            $result = $user->login($request->getPost('login'), $request->getPost('passwd'),$path);
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
            $this->schoolTable = $sm->get('School\Model\SchoolTable');
        }
        return $this->schoolTable;
        }
        
	public function getCommentTable()
        {
        if (!$this->commentTable) {
            $sm = $this->getServiceLocator();
            $this->commentTable = $sm->get('School\Model\CommentTable');
        }
        return $this->commentTable;
        }
        
	public function updatenewsAction()
        {
            $confArray = $this->getServiceLocator()->get('config');
            $confRow = $confArray['rss'];
            $url = $confRow['url'];
            $file = User::getDocumentRoot().'/'.$confRow['file'];
            $rss = new Rss($url);
            $writer = new XmlWriter();
            $config = $rss->__get('channel');
            $writer->toFile($file,$config);
        return $this->redirect()->toRoute('school');
        }        
}
