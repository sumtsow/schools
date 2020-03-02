<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Application\Model\BranchTable;
use Application\Model\CommentTable;
use Application\Model\FormTable;
use Application\Model\LevelTable;
use Application\Model\ProgramTable;
use Application\Model\ProgrambachTable;
use Application\Model\RegionTable;
use Application\Model\SchoolTable;
use Application\Model\SearchTable;
use Application\Model\SpecialtyTable;
use Application\Model\SubjectTable;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        /* Define app locale */
		
        session_start();
		if(empty($_SESSION['locale'])) {
			$_SESSION['locale'] = false;
		}
        $query = $e->getApplication()->getRequest()->getQuery();
        if(null != $query->locale) {
            $locale = $query->locale;
            $_SESSION['locale'] = $locale;
        }
        elseif(null != $_SESSION['locale']) {
            $locale = $_SESSION['locale'];
        }
        else {
            $config = $e->getApplication()->getServiceManager()->get('config');
            $locale = $config['translator']['locale'];
        }
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $translator->setLocale($locale);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    	
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Model\BranchTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new BranchTable($dbAdapter);
                    return $table;
                },
                'Application\Model\CommentTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new CommentTable($dbAdapter);
                    return $table;
                },						
                'Application\Model\FormTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new FormTable($dbAdapter);
                    return $table;
                },
                'Application\Model\LevelTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new LevelTable($dbAdapter);
                    return $table;
                },
                'Application\Model\ProgramTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new ProgramTable($dbAdapter);
                    return $table;
                },
                'Application\Model\ProgrambachTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new ProgrambachTable($dbAdapter);
                    return $table;
                },
				'Application\Model\RegionTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new RegionTable($dbAdapter);
                    return $table;
                },
                'Application\Model\SchoolTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new SchoolTable($dbAdapter);
                    return $table;
                },                
				'Application\Model\SubjectTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new SubjectTable($dbAdapter);
                    return $table;
                },
				'Application\Model\SearchTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new SearchTable($dbAdapter);
                    return $table;
                },
				'Application\Model\SpecialtyTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new SpecialtyTable($dbAdapter);
                    return $table;
                },
            ),
        );
    }
}
