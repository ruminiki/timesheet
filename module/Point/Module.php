<?php
namespace Point;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Point\Model\Point;
use Point\Model\PointTable;

use Point\Model\WorkedHours;
use Point\Model\WorkedHoursTable;

use Point\Model\DayNotWorked;
use Point\Model\DayNotWorkedTable;

use Point\Model\Config;
use Point\Model\ConfigTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    // Add this method:
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Point\Model\PointTable' =>  function($sm) {
                    $tableGateway = $sm->get('PointTableGateway');
                    $table = new PointTable($tableGateway);
                    return $table;
                },
                'PointTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Point());
                    return new TableGateway('point', $dbAdapter, null, $resultSetPrototype);
                },
                'Point\Model\WorkedHoursTable' =>  function($sm) {
                    $tableGateway = $sm->get('WorkedHoursTableGateway');
                    $table = new WorkedHoursTable($tableGateway);
                    return $table;
                },
                'WorkedHoursTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new WorkedHours());
                    return new TableGateway('worked_hours', $dbAdapter, null, $resultSetPrototype);
                },
                'Point\Model\DayNotWorkedTable' =>  function($sm) {
                    $tableGateway = $sm->get('DayNotWorkedTableGateway');
                    $table = new DayNotWorkedTable($tableGateway);
                    return $table;
                },
                'DayNotWorkedTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DayNotWorked());
                    return new TableGateway('day_not_worked', $dbAdapter, null, $resultSetPrototype);
                },
                'Point\Model\ConfigTable' =>  function($sm) {
                    $tableGateway = $sm->get('ConfigTableGateway');
                    $table = new ConfigTable($tableGateway);
                    return $table;
                },
                'ConfigTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Config());
                    return new TableGateway('config', $dbAdapter, null, $resultSetPrototype);
                },

            ),
        );
    }

}