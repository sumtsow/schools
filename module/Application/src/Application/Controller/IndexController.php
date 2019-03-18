<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Application\Model\User;
use Application\Form\UserForm;
use Application\Model\Rss;
use Application\Model\School;
use Application\Form\SchoolForm;
use Application\Form\CommentForm;

class IndexController extends AbstractActionController
{
    
    protected $schoolTable;
    protected $commentTable;
    
    public function indexAction()
    {
        $confArray = $this->getServiceLocator()->get('config');
        $news = new Rss($this->request->getUri()->getScheme().'://'.$this->request->getUri()->getHost().'/'.$confArray['rss']['file']);
        $area = ($this->request->getPost('area')) ? $this->request->getPost('area') : $this->params()->fromRoute('area', 0);
        $id = ($this->params()->fromRoute('id')) ? $this->params()->fromRoute('id') : 0;
        $result = $this->getSchoolTable()->fetchSchools($id, $area);
        $paginator = new Paginator(new ArrayAdapter($result));
        $page = ($this->request->getPost('area')) ? 0 : $this->params()->fromRoute('page');            
        $paginator->setCurrentPageNumber($page)    
            ->setItemCountPerPage($confArray['per_page'])
            ->setPageRange(ceil(count($result)/$confArray['per_page']));
        $user = new User();
        $vm = new ViewModel();
        return $vm->setVariable('paginator', $paginator)
            ->setVariable('areas', $this->getSchoolTable()->fetchAreas())
            ->setVariable('high', $id)
            ->setVariable('area', $area)
            ->setVariable('news', $news)
            ->setVariable('news_max', $confArray['rss']['max'])
            ->setVariable('username', ($user->isValid()) ? $user->getLogin() : null);
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
            ->setVariable('username', ($user->isValid()) ? $user->getLogin() : null);
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
                return $this->redirect()->toRoute('schools', array('action' => 'view', 'id' => $id));
            }
        }
        return $vm->setVariable('form', $form);
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
                $result = $user->login($request->getPost('login'), $request->getPost('passwd'), User::getDocumentRoot().'/secure');
                return $this->redirect()->toRoute('schools', array('action' => 'index'));
            }
        }
    
    return array(
        'form' => $form,
        'username' => $user->getLogin(),
    );
    
    }
    
    public function logoutAction()
    {
        $user = new User();
        $user->logout();
        return $this->redirect()->toRoute('schools');
    } 
    
    public function getSchoolTable()
    {
        if (!$this->schoolTable) {
            $sm = $this->getServiceLocator();
        }
    return $sm->get('Application\Model\SchoolTable');
    }
        
    public function getCommentTable()
    {
        if (!$this->commentTable) {
            $sm = $this->getServiceLocator();
        }
        return $sm->get('Application\Model\CommentTable');
    }
}
