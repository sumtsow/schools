<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'schools' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '[/:action][/:id][/:area][/:page][/:api][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'area'     => '[0-9]',
                        'page'     => '[0-9]+',
                        'api'     => '[a-zA-Z]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                ),
            ),
            'admin' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/admin[/:action][/:id][/:area][/:page][/]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'area'     => '[0-9]',
                        'page'     => '[0-9]+',                                
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Admin',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                ),
            ),
            'program' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/program[/:action][/:id][/]',
                    'constraints' => array(
                        'action'     => '[a-z]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Program',
                        'action'     => 'index',
						'id'     => '[0-9]+',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                ),
            ),
            'import' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/import[/:action][/:id][/]',
                    'constraints' => array(
                        'action'     => '[a-z]*',
						'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Import',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                ),
            ),
			'dbimport' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/dbimport[/:page][/:action][/:id][/]',
                    'constraints' => array(
						'page'     => '[0-9]+',
                        'action'     => '[a-z]*',
						'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Dbimport',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                ),
            ),
            'search' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/search[/:action][/]',
                    'constraints' => array(
                        'action'     => '[a-z]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Search',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                ),
            ),
            'specialty' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/specialty[/:action][/:id][/:api][/]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[0-9]+',
						'api'     => '[a-zA-Z]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Specialty',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                ),
            ),
            'subject' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/subject[/:action][/:id][/]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Subject',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                ),
            ),			
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
       'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        /*'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),*/
    ),
    'translator' => array(
        //'locale' => 'en',
        'defaultlocale' => 'en_US',
        'locale' => 'uk',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Admin' => 'Application\Controller\AdminController',
            //'Application\Controller\Dbimport' => 'Application\Controller\DbimportController',			
            //'Application\Controller\Import' => 'Application\Controller\ImportController',
			'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Program' => 'Application\Controller\ProgramController',
            'Application\Controller\Search' => 'Application\Controller\SearchController',
			'Application\Controller\Specialty' => 'Application\Controller\SpecialtyController',
			'Application\Controller\Subject' => 'Application\Controller\SubjectController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
		'strategies' => array(
			'ViewJsonStrategy',
		),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
