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

use Application\Model\Level;
use Application\Model\Search;
use Application\Model\Subject;
use Application\Model\User;

class SearchController extends AbstractActionController
{
    protected $formTable;
    protected $levelTable;
	protected $subjectTable;
	protected $searchTable;

    public function indexAction()
    {
        $vm = new ViewModel();
        return $vm->setVariable('title', 'University search')
        	->setVariable('forms', $this->getFormTable()->fetchAll())
			->setVariable('levels', $this->getLevelTable()->fetchAll())
			->setVariable('subjects', $this->getSubjectTable()->fetchAll());
    }
	
	public function viewAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $subjects = $request->getPost()->subjects;
            $level = $request->getPost()->level;
            $form = ($request->getPost()->form) ? $request->getPost()->form : false;
			$result = $this->getSearchTable()->getPrograms($subjects, $level, $form);
			$vm = new ViewModel();
			return $vm->setVariable('search', $result);			
        }
		return $this->redirect()->toRoute('search');
    }

    public function localeAction()
    {
        $locale = new User();
        $user->logout();
        return $this->redirect()->toRoute('schools');
    }
        	    
    public function getFormTable()
    {
        if (!$this->formTable) {
            $sm = $this->getServiceLocator();
        }
        return $sm->get('Application\Model\FormTable');
    }
    
    public function getLevelTable()
    {
        if (!$this->levelTable) {
            $sm = $this->getServiceLocator();
        }
        return $sm->get('Application\Model\LevelTable');
    }
    
    public function getSubjectTable()
    {
        if (!$this->subjectTable) {
            $sm = $this->getServiceLocator();
        }
        return $sm->get('Application\Model\SubjectTable');
    }
	    
    public function getSearchTable()
    {
        if (!$this->searchTable) {
            $sm = $this->getServiceLocator();
        }
        return $sm->get('Application\Model\SearchTable');
    }
}
