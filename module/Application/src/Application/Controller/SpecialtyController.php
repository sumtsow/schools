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
use Application\Model\User;

class SpecialtyController extends AbstractActionController
{

	protected $branchTable;    
    protected $programTable;
    protected $schoolTable;
    protected $specialtyTable;
    
    public function indexAction()
    {
		$api = $this->params()->fromRoute('api');
		$domDocument = $this->getSpecialtyTable()->getBranchesDOM();
		switch ($api) {
			case 'json':
				$data = $this->getSpecialtyTable()->saveJSON($domDocument);
				break;
			case 'xml':
				$data = $domDocument->saveXML();
				break;
			default:
				$data = $domDocument;
				break;
		}
        $vm = new ViewModel();
        return $vm->setVariable('data', $data)->setVariable('api', $api);
    }
	
    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $vm = new ViewModel();
		$specialty = $this->getSpecialtyTable()->fetchOne($id);
		$id_schools = $this->getProgramTable()->getSchools($id);
		$schools = $this->getSchoolTable()->fetch($id_schools);
		$branch = $this->getBranchTable()->fetchOne($specialty->id_branch);
        return $vm->setVariable('branch', $branch)
			->setVariable('schools', $schools)
			->setVariable('specialty', $specialty);
    }
     
    public function localeAction()
    {
        $locale = new User();
        $user->logout();
        return $this->redirect()->toRoute('schools');
    } 
    
    public function getBranchTable()
    {
        if (!$this->branchTable) {
            $sm = $this->getServiceLocator();
			$this->branchTable = $sm->get('Application\Model\BranchTable');
        }
    return $this->branchTable;
    }

    public function getProgramTable()
    {
        if (!$this->programTable) {
            $sm = $this->getServiceLocator();
			$this->programTable = $sm->get('Application\Model\ProgramTable');
        }
    return $this->programTable;
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
