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
        $journey_weekly = $this->getConfigTable()->getValueByKey(Config::JORNADA_SEMANAL);

        $business_days = $this->getDayNotWorkedTable()->getBusinessDaysByMonth(date('Y').date('m'), $journey_weekly);
        $journey_daily = $this->getConfigTable()->getValueByKey(Config::JORNADA_DIARIA);
        $worked_hours_month = $this->getWorkedHoursTable()->getSumWorkedHoursMonth(date('Y').date('m'));

        $monthly_balance = $worked_hours_month - ($business_days * $journey_daily);
        $overall_balance = $this->getOverallBalance(date('Y').date('m'));

        return new ViewModel(array(
            'points' => $points,
            'year' => date('Y'),
            'month' => date('m'),
            'worked_hours_month' => $worked_hours_month,
            'month_label' => strftime("%B",time()),
            'business_days' => $business_days,
            'journey_daily' => $journey_daily,
            'monthly_balance' => $monthly_balance,
            'overall_balance' => $overall_balance,            
        ));

    }

    public function runReportAction()
    {

        $year_month = $this->params()->fromRoute('date', 0);
        $year_month = str_replace(' ', '', $year_month);
        
        $year = substr($year_month, 0, 4);
        $month = substr($year_month, 4, 2);

        $points = $this->getPointTable()->fetchAllByMonth($year_month);
        $journey_weekly = $this->getConfigTable()->getValueByKey(Config::JORNADA_SEMANAL);

        $business_days = $this->getDayNotWorkedTable()->getBusinessDaysByMonth($year_month, $journey_weekly);
        $journey_daily = $this->getConfigTable()->getValueByKey(Config::JORNADA_DIARIA);
        $worked_hours_month = $this->getWorkedHoursTable()->getSumWorkedHoursMonth($year_month);

        $monthly_balance = $worked_hours_month - ($business_days * $journey_daily);
        $overall_balance = $this->getOverallBalance($year_month);      

        $viewModel = new ViewModel(array(
            'points' => $points,
            'year' => $year,
            'month' => $month,
            'worked_hours_month' => $worked_hours_month,
            'month_label' => strftime("%B", strtotime($year_month.'01')),
            'business_days' => $business_days,
            'journey_daily' => $journey_daily,
            'monthly_balance' => $monthly_balance,
            'overall_balance' => $overall_balance,            
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

    public function getOverallBalance($year_month_limit)
    {
        //retorna o primeiro mes com registro de ponto. Formato Ym
        $first_year_month_worked = $this->getPointTable()->getFirstYearMonthWorked();
        $overall_balance = (int) $this->getConfigTable()->getValueByKey(Config::SALDO_INICIAL_BANCO_DE_HORAS);

        while ($first_year_month_worked <= $year_month_limit) {

            $journey_daily = $this->getConfigTable()->getValueByKey(Config::JORNADA_DIARIA);
            $journey_weekly = $this->getConfigTable()->getValueByKey(Config::JORNADA_SEMANAL);

            $business_days = $this->getDayNotWorkedTable()->getBusinessDaysByMonth($first_year_month_worked, $journey_weekly);
            
            $worked_hours_month = $this->getWorkedHoursTable()->getSumWorkedHoursMonth($first_year_month_worked);

            $monthly_balance = $worked_hours_month - ($business_days * $journey_daily);
            $overall_balance += $monthly_balance;

            $month = (int) substr($first_year_month_worked, 4,2);

            if ( $month == 12 ){
                $month = '01';
                $year = ((int) substr($first_year_month_worked, 0,4)) + 1;

                $first_year_month_worked = $year.$month;
            }else{
                $month = ((int) substr($first_year_month_worked, 4,2)) + 1;
                
                if ( $month < 10 ){
                    $month = '0'.$month;
                }

                $first_year_month_worked = substr($first_year_month_worked, 0,4) . $month;
            }

        }

        return $overall_balance;
    }

}