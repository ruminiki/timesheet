<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Point\Controller\Point' => 'Point\Controller\PointController',
            'Point\Controller\Report' => 'Point\Controller\ReportController',
        ),
    ),
     // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'point' => array(
                'type'    => 'segment',
                'options' => array(
                    //'route'    => '/point[/][:action][/][:id][:date]',
                    'route'    => '/point[/][:action][/][:id][/][:date]',
                    //'route'    => '/point[/][:action][/:date]',
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
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'point' => __DIR__ . '/../view',
        ),
    ),
);