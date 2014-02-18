<?php
namespace Point\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Point\Form\ReportForm;
use Point\Model\Report;
use Zend\Session\Container;
use DateTime;

class ReportController extends AbstractActionController
{
    protected $pointTable;
    protected $workedHoursTable;
        
    public function indexAction()
    {
        $points = $this->getPointTable()->fetchAllByMonth('02');
              
        return new ViewModel(array(
            'points' => $points,
            'worked_hours_month' => $this->getWorkedHoursTable()->getSumWorkedHoursMonth('02'),
        ));

    }

    public function getPointTable()
    {
        if (!$this->pointTable) {
            $sm = $this->getServiceLocator();
            $this->pointTable = $sm->get('Point\Model\PointTable');
        }
        return $this->pointTable;
    }

    public function getWorkedHoursTable()
    {
        if (!$this->workedHoursTable) {
            $sm = $this->getServiceLocator();
            $this->workedHoursTable = $sm->get('Point\Model\WorkedHoursTable');
        }
        
        return $this->workedHoursTable;
    }


}