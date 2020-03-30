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
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

use Zend\View\Model\ViewModel;
use Application\Model\User;
use Application\Form\UserForm;
use Application\Model\Rss;
use Application\Form\CommentForm;

class IndexController extends AbstractActionController
{

    protected $commentTable;
    protected $programTable;
    protected $regionTable;
    protected $schoolTable;
    protected $specialtyTable;
    
    public function indexAction()
    {
        $confArray = $this->getServiceLocator()->get('config');
        $news = new Rss($this->request->getUri()->getScheme().'://'.$this->request->getUri()->getHost().'/'.$confArray['rss']['file']);
        $area = ($this->request->getPost('area')) ? $this->request->getPost('area') : $this->params()->fromRoute('area', 0);
        $id = ($this->params()->fromRoute('id')) ? $this->params()->fromRoute('id') : 0;
		$id_region = $this->params()->fromQuery('region');
		$id_region = $id_region ? $id_region : 0;
		$type = $this->params()->fromQuery('type');
		$type = $type ? $type : null;
		$locale = $this->getServiceLocator()->get('translator')->getLocale();
        $schools = ($id == 1) ? $this->getSchoolTable()->fetchUniversities($id_region, ['field' => 'name_'.$locale, 'order' => 'ASC'], $type, 1) : $this->getSchoolTable()->fetchSchools($area);
        $paginator = new Paginator(new ArrayAdapter($schools));
        $page = ($this->request->getPost('area')) ? 0 : $this->params()->fromRoute('page');            
        $paginator->setCurrentPageNumber($page)    
            ->setItemCountPerPage($confArray['per_page'])
            ->setPageRange(ceil(count($schools)/$confArray['per_page']));
        $user = new User();
		$regions = $this->getRegionTable()->fetchAll();
		$types = $this->getSchoolTable()->fetchTypes();
		array_pop($types);
		array_pop($types);
		array_pop($types);
        $vm = new ViewModel();
        return $vm->setVariable('paginator', $paginator)
            ->setVariable('areas', $this->getSchoolTable()->fetchAreas())
            ->setVariable('high', $id)
            ->setVariable('area', $area)
            ->setVariable('news', $news)
			->setVariable('regions', $regions)
			->setVariable('id_region', $id_region)
			->setVariable('types', $types)
			->setVariable('type', $type)
            ->setVariable('news_max', $confArray['rss']['max'])
            ->setVariable('username', ($user->isValid()) ? $user->getLogin() : null);
    }
	
    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id', false);
		if(!$id) {
			return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
		}
        $vm = new ViewModel();
        $user = new User();
		$locale = $this->getServiceLocator()->get('translator')->getLocale();
		$school = $this->getSchoolTable()->fetchOne($id);
		$vm->setVariable('school', $school)
			->setVariable('region', $this->getProgramTable()->getRegionTitle($school->id_region))
            ->setVariable('comments', $this->getCommentTable()->fetchComments($id))
            ->setVariable('docRoot', User::getDocumentRoot())
            ->setVariable('username', ($user->isValid()) ? $user->getLogin() : null);
		if($vm->school->high) {
			$programs = $this->getProgramTable()->getProgramsByIdSchool($id);
			if($programs) {
				$id_specialties = $this->getProgramTable()->getSpecialtiesId($programs);
				$specialties = $this->getSpecialtyTable()->fetch($id_specialties);
				$specialtyDOM = $this->getProgramTable()->getSpecialtyDOM($id, $locale);
				$vm->setVariable('specialties', $specialties)
					->setVariable('specialtyDOM', $specialtyDOM)
					->setVariable('api', $this->params()->fromRoute('api'));
			}
		}
        if(!$school->visible) { return $this->redirect()->toRoute('schools', ['action' => 'index']); }
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
    
    public function findAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $search = $request->getPost('search');
            if ($search) {
                $results = $this->getSchoolTable()->search($search);
                return [ 'results' => $results, 'search' => $search ];
            }
        return $this->redirect()->toRoute('schools');            
        }
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
     
    public function localeAction()
    {
        $locale = new User();
        $user->logout();
        return $this->redirect()->toRoute('schools');
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
}
