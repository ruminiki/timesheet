<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Point\Controller\Point' => 'Point\Controller\PointController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'point' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/point[/][:action][/:id/:date]',
                     'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'date'   => '[0-9]+',
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