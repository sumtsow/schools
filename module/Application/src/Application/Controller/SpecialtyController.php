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
    
    protected $specialtyTable;
    
    public function indexAction()
    {
        $vm = new ViewModel();
        return $vm->setVariable('dom', $this->getSpecialtyTable()->getBranchesDOM());
    }
	
    public function viewAction()
    {
        /*$id = (int) $this->params()->fromRoute('id', 0);*/
        $vm = new ViewModel();
		/*$vm->setVariable('school',$this->getSchoolTable()->getSchool($id));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $comment = $this->getCommentTable()->getComment($request->getPost('id'));
            $formData['id_school'] = $id;
            $form->setData($formData);
            if ($form->isValid()) {
                return $this->redirect()->toRoute('schools', array('action' => 'view', 'id' => $id));
            }
        }*/
        return $vm->setVariable('form', '$form');
    }
     
    public function localeAction()
    {
        $locale = new User();
        $user->logout();
        return $this->redirect()->toRoute('schools');
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
