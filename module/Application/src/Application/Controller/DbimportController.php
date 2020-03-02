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

class DbimportController extends AbstractActionController
{

	protected $commentTable;
    protected $programTable;
	protected $programbachTable;
	protected $regionTable;
    protected $schoolTable;
    protected $specialtyTable;
    
    public function indexAction()
    {
		$confArray = $this->getServiceLocator()->get('config');
        $programs = $this->getProgrambachTable()->fetchAll();
		$paginator = new Paginator(new ArrayAdapter($programs));
		$page = $this->params()->fromRoute('page');
        $paginator->setCurrentPageNumber($page)    
            ->setItemCountPerPage($confArray['per_page'])
            ->setPageRange(ceil(count($programs)/$confArray['per_page']));
		$vm = new ViewModel();
        return $vm->setVariable('paginator', $paginator);
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
		$school = $this->getSchoolTable()->fetch($id);
		return $vm;
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
	
    public function getProgrambachTable()
    {
        if (!$this->programbachTable) {
            $sm = $this->getServiceLocator();
			$this->programbachTable = $sm->get('Application\Model\ProgrambachTable');
        }
    return $this->programbachTable;
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
