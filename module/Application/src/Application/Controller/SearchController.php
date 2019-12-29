<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Db\RowGateway\RowGateway;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\Http\Response;

use Application\Model\Level;
use Application\Model\Search;
use Application\Model\Subject;
use Application\Model\User;
use Application\Form\UserForm;

class SearchController extends AbstractActionController
{
    protected $formTable;
    protected $levelTable;
	protected $subjectTable;
	protected $searchTable;

    public function indexAction()
    {
        $vm = new ViewModel();
        return $vm->setVariable('forms', $this->getFormTable()->fetchAll())
			->setVariable('levels', $this->getLevelTable()->fetchAll())
			->setVariable('subjects', $this->getSubjectTable()->fetchAll())
			->setVariable('error', null);
    }
	
	public function ratingAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
			$id_subject = $request->getPost()->subjects;
			$subjects = $this->getSubjectTable()->fetch($id_subject);
			if(count($subjects) > 2) {
			    $vm = new ViewModel();			
			    return $vm->setVariable('subjects', $subjects)
				    ->setVariable('level', $request->getPost()->level)
				    ->setVariable('form', ($request->getPost()->form) ? $request->getPost()->form : false);
			}
        }
		return $this->redirect()->toRoute('search');
    }
	
	public function viewAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $rating = $request->getPost()->subject;
            $level = $request->getPost()->level;
            $form = ($request->getPost()->form) ? $request->getPost()->form : false;
			$result = $this->getSearchTable()->getPrograms($rating, $level, $form);
			$vm = new ViewModel();
			return $vm->setVariable('search', $result);			
        }
		return $this->redirect()->toRoute('search');
    }

    public function localeAction()
    {
        $locale = new User();
        $user->logout();
        return $this->redirect()->toRoute('search');
    }
        	    
    public function getFormTable()
    {
        if (!$this->formTable) {
            $sm = $this->getServiceLocator();
			$this->formTable = $sm->get('Application\Model\FormTable');
        }
        return $this->formTable;
    }
    
    public function getLevelTable()
    {
        if (!$this->levelTable) {
            $sm = $this->getServiceLocator();
			$this->levelTable = $sm->get('Application\Model\LevelTable');
        }
        return $this->levelTable;
    }
    
    public function getSubjectTable()
    {
        if (!$this->subjectTable) {
            $sm = $this->getServiceLocator();
			$this->subjectTable = $sm->get('Application\Model\SubjectTable');
        }
        return $this->subjectTable;
    }
	    
    public function getSearchTable()
    {
        if (!$this->searchTable) {
            $sm = $this->getServiceLocator();
			$this->searchTable = $sm->get('Application\Model\SearchTable');
        }
        return $this->searchTable;
    }
}
