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
use Application\Model\Program;
use Application\Model\Subject;
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
		$user = new User();
		if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
		$confArray = $this->getServiceLocator()->get('config');
        $programs = $this->getProgrambachTable()->fetchAll()->toArray();
		$id_edbo = $this->getProgrambachTable()->getSchoolIdEdbo();
		$schools = $this->getSchoolTable()->getSchoolByIdEdbo($id_edbo)->toArray();
		$paginator = new Paginator(new ArrayAdapter($schools));
		$page = $this->params()->fromRoute('page');
        $paginator->setCurrentPageNumber($page)    
            ->setItemCountPerPage($confArray['per_page'])
            ->setPageRange(ceil(count($schools)/$confArray['per_page']));
		$vm = new ViewModel();
        return $vm->setVariable('paginator', $paginator)
				->setVariable('programs', $programs);
    }
	
    public function viewAction()
    {
		$user = new User();
		if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
        $id_edbo = (int) $this->params()->fromRoute('id', false);
		if(!$id_edbo) {
			return $this->redirect()->toRoute('dbimport', array(
                'action' => 'index'
            ));
		}
		$school = $this->getSchoolTable()->getSchoolByIdEdbo($id_edbo)->current();
		$programs = $this->getProgrambachTable()->getProgramsBySchool($id_edbo);
		$vm = new ViewModel();
		return $vm->setVariable('school', $school)
				->setVariable('programs', $programs);
    }
	
    public function showAction()
    {
		$user = new User();
		if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
        $id_edbo = (int) $this->params()->fromRoute('id', false);
		if(!$id_edbo) {
			return $this->redirect()->toRoute('dbimport', array(
                'action' => 'index'
            ));
		}
		$program = $this->getProgrambachTable()->fetch($id_edbo);
		$dbProgram = $this->getProgrambachTable()->getProgram($id_edbo);
		$newProgram = new Program();
        $newProgram->exchangeArray($dbProgram);
		$subject = new Subject();
		$subject->exchangeArray($dbProgram['subjects']);
		$result1 = $this->getProgramTable()->saveProgram($newProgram);
		$id_program = $this->getProgramTable()->fetchIdEDBO($id_edbo)->id;
		$subject->id_program = $id_program;
		$result2 = $this->getSubjectTable()->save($subject);
		$id_school_edbo = $this->params()->fromQuery('id_school');
		$school = $this->getSchoolTable()->getSchoolByIdEdbo($id_school_edbo)->current();
		$vm = new ViewModel();
		return $vm->setVariable('school', $school)
				->setVariable('program', $program)
				->setVariable('dbProgram', $dbProgram);
    }
	
	public function importallAction() {
		$user = new User();
		if (!$user->isValid()) {
            return $this->redirect()->toRoute('schools', array(
                'action' => 'index'
            ));
        }
        $programs = $this->getProgrambachTable()->fetchAll();
		foreach($programs as $program) {
			if($program->Name) {
				$dbProgram = $this->getProgrambachTable()->getProgram($program->ExternalId);
				$newProgram = new Program();
				$newProgram->exchangeArray($dbProgram);
				$subject = new Subject();
				$subject->exchangeArray($dbProgram['subjects']);
				$result1 = $this->getProgramTable()->saveProgram($newProgram);
				$id_program = $this->getProgramTable()->fetchIdEDBO($program->ExternalId)->id;
				$subject->id_program = $id_program;
				$result2 = $this->getSubjectTable()->save($subject);
			}
		}
        return $this->redirect()->toRoute('admin', ['action' => 'index', 'id' => 1]);
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
	
	public function getSubjectTable()
    {
        if (!$this->subjectTable) {
            $sm = $this->getServiceLocator();
			$this->subjectTable = $sm->get('Application\Model\SubjectTable');
        }
    return $this->subjectTable;
    }
}
