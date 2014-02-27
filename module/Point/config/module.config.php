<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Point\Controller\Point' => 'Point\Controller\PointController',
            'Point\Controller\Report' => 'Point\Controller\ReportController',
            'Point\Controller\DayNotWorked' => 'Point\Controller\DayNotWorkedController',
            'Point\Controller\Config' => 'Point\Controller\ConfigController',
        ),
    ),
     // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'point' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/point[/][:action][/][:id][/][:date]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'date'   => '[0-9]+',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Point\Controller\Point',
                        'action'     => 'index',
                    ),
                ),
            ),

            'report' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/report[/][:action][/][:date]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Point\Controller\Report',
                        'action'     => 'index',
                    ),
                ),
            ),


            'day-not-worked' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/day-not-worked[/][:action][/][:date]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Point\Controller\DayNotWorked',
                        'action'     => 'index',
                    ),
                ),
            ),

            'config' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/config[/][:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Point\Controller\Config',
                        'action'     => 'index',
                    ),
                ),
            ),
            
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'point' => __DIR__ . '/../view',
        ),
    ),
);