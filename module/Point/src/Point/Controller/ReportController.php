<?php
namespace Point\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Point\Form\ReportForm;
use Point\Model\Report;
use Point\Model\Config;
use Zend\Session\Container;
use DateTime;

class ReportController extends AbstractActionController
{
    protected $pointTable;
    protected $workedHoursTable;
    protected $dayNotWorkedTable;
    protected $configTable;
        
    public function indexAction()
    {
        $points = $this->getPointTable()->fetchAllByMonth(date('Y').date('m'));
        $work_days_in_week = $this->getConfigTable()->getValueByKey(Config::JORNADA_SEMANAL);

        return new ViewModel(array(
            'points' => $points,
            'year' => date('Y'),
            'month' => date('m'),
            'worked_hours_month' => $this->getWorkedHoursTable()->getSumWorkedHoursMonth(date('Y').date('m')),
            'month_label' => strftime("%B",time()),
            'business_days' => $this->getDayNotWorkedTable()->getBusinessDaysByMonth(date('Y').date('m'), $work_days_in_week),
            'journey_daily' => $this->getConfigTable()->getValueByKey(Config::JORNADA_TRABALHO_SEMANAL),
        ));

    }

    public function runReportAction()
    {

        $year_month = $this->params()->fromRoute('date', 0);
        $year_month = str_replace(' ', '', $year_month);
        
        $year = substr($year_month, 0, 4);
        $month = substr($year_month, 4, 2);

        $points = $this->getPointTable()->fetchAllByMonth($year_month);
        $work_days_in_week = $this->getConfigTable()->getValueByKey(Config::JORNADA_SEMANAL);              

        $viewModel = new ViewModel(array(
            'points' => $points,
            'year' => $year,
            'month' => $month,
            'worked_hours_month' => $this->getWorkedHoursTable()->getSumWorkedHoursMonth($year_month),
            'month_label' => strftime("%B", strtotime($year_month.'01')),
            'business_days' => $this->getDayNotWorkedTable()->getBusinessDaysByMonth($year_month, $work_days_in_week),
            'journey_daily' => $this->getConfigTable()->getValueByKey(Config::JORNADA_TRABALHO_SEMANAL),
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

    public function getDayNotWorkedTable()
    {
        if (!$this->dayNotWorkedTable) {
            $sm = $this->getServiceLocator();
            $this->dayNotWorkedTable = $sm->get('Point\Model\DayNotWorkedTable');
        }
        
        return $this->dayNotWorkedTable;
    }

    public function getConfigTable()
    {
        if (!$this->configTable) {
            $sm = $this->getServiceLocator();
            $this->configTable = $sm->get('Point\Model\ConfigTable');
        }
        
        return $this->configTable;
    }

}