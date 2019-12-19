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
use Zend\I18n\Translator\Translator;
use Zend\Paginator\Adapter\ArrayAdapter;

use Zend\View\Model\ViewModel;
use Application\Model\Level;
use Application\Model\Subject;
use Application\Model\User;

class SearchController extends AbstractActionController
{
    
    protected $levelTable;
	protected $subjectTable;

    public function indexAction()
    {
        $vm = new ViewModel();
        return $vm->setVariable('title', 'University search')
			->setVariable('levels', $this->getLevelTable()->fetchAll())
			->setVariable('subjects', $this->getSubjectTable()->fetchAll());
    }
	
	public function viewAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
			$id_subject = $request->getPost()->subject;
			$id_level = $request->getPost()->level;
            //$subject = new Subject();
			//$level = new Level();
            /*$form->setInputFilter([
				$subject->getInputFilter(),
				$level->getInputFilter(),
			]);*/
			$vm = new ViewModel();
			return $vm->setVariable('title', 'University search')
				->setVariable('level', $id_level)
				->setVariable('subject', $id_subject);			
        }
		return $this->redirect()->toRoute('search');
    }

    public function localeAction()
    {
        $locale = new User();
        $user->logout();
        return $this->redirect()->toRoute('schools');
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
}
