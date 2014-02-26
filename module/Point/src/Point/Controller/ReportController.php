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
        $points = $this->getPointTable()->fetchAllByMonth(date('Y').date('m'));
              
        return new ViewModel(array(
            'points' => $points,
            'year' => date('Y'),
            'month' => date('m'),
            'worked_hours_month' => $this->getWorkedHoursTable()->getSumWorkedHoursMonth(date('Y').date('m')),
            'month_label' => date('F'),
        ));

    }

    public function runReportAction()
    {

        $year_month = $this->params()->fromRoute('date', 0);
        $year_month = str_replace(' ', '', $year_month);
        
        $year = substr($year_month, 0, 4);
        $month = substr($year_month, 4, 2);

        $points = $this->getPointTable()->fetchAllByMonth($year_month);
              
        $viewModel = new ViewModel(array(
            'points' => $points,
            'year' => $year,
            'month' => $month,
            'worked_hours_month' => $this->getWorkedHoursTable()->getSumWorkedHoursMonth($year_month),
            'month_label' => date("F", strtotime($year_month.'01')),
        ));

        return $viewModel->setTemplate('point/report/index.phtml');

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