<?php
namespace Point\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Point\Form\DayNotWorkedForm;
use Point\Model\DayNotWorked;
use Zend\Session\Container;
use DateTime;

class DayNotWorkedController extends AbstractActionController
{
    protected $dayNotWorkedTable;
        
    public function indexAction()
    {
        
        $year_month = $this->params()->fromRoute('date', 0);
        $year = "";
        $month = "";

        if ( !empty($year_month) ){
            $year_month = str_replace(' ', '', $year_month);
            
            $year = substr($year_month, 0, 4);
            $month = substr($year_month, 4, 2);    
        }else{
            $year = date('Y');
            $month = date('m');
        }

        $days_not_worked = $this->getDayNotWorkedTable()->fetchAllByMonth($year.$month);
              
        return new ViewModel(array(
            'days_not_worked' => $days_not_worked,
            'year' => $year,
            'month' => $month,
        ));

    }

    public function fetchByMonthAction()
    {

        $year_month = $this->params()->fromRoute('date', 0);
        $year_month = str_replace(' ', '', $year_month);
        
        $year = substr($year_month, 0, 4);
        $month = substr($year_month, 4, 2);

        $days_not_worked = $this->getDayNotWorkedTable()->fetchAllByMonth($year_month);
              
        $viewModel = new ViewModel(array(
            'days_not_worked' => $days_not_worked,
            'year' => $year,
            'month' => $month,
        ));

        return $viewModel->setTemplate('point/day-not-worked/index.phtml');

    }

    public function addAction()
    {
        $date = date('Ymd');
        $formated_date = date('d/m/Y');
        return array(
            'date' => $date,
            'formated_date' => $formated_date,
        );
    }


    public function markDayAsNotWorkedAction(){

        $year_month = $this->params()->fromRoute('date', 0);

        if (!$year_month) {
            return $this->redirect()->toRoute('day-not-worked');
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $option = $request->getPost('option', 'Cancelar');

            if ($option == 'Salvar') {
                //verify if user was mark a period    
                $end_date_period = $request->getPost('datepicker-end-period-not-worked');
                $start_date = DateTime::createFromFormat( "d/m/Y", $request->getPost('datepicker-start-period-not-worked') );
                
                if ( !empty($end_date_period) ){
                    $end_date   = DateTime::createFromFormat( "d/m/Y", $end_date_period );
                    while ($start_date <= $end_date){
                        $reason = $request->getPost('reason');
                        $this->getDayNotWorkedTable()->markDayAsNotWorked($start_date->format("Ymd"), $reason);
                        $start_date->modify( 'next day' );
                    }
                }else{
                    $reason = $request->getPost('reason');
                    $this->getDayNotWorkedTable()->markDayAsNotWorked($start_date->format("Ymd"), $reason);
                }    

                $year_month = $start_date->format("Ym");

            }
            
        }
        // Redirect to list of points
        $year_month = str_replace(' ', '', $year_month);
        $year = substr($year_month, 0, 4);
        $month = substr($year_month, 4, 2);    

        $viewModel = new ViewModel(array(
            'days_not_worked' => $this->getDayNotWorkedTable()->fetchAllByMonth($year_month),
            'year' => $year,
            'month' => $month,
        ));

        return $viewModel->setTemplate('point/day-not-worked/index.phtml');
    }

    public function deleteAction()
    {
        $date = $this->params()->fromRoute('date', 0);
        if (!$date) {
            return $this->redirect()->toRoute('day-not-worked');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'Não');

            if ($del == 'Sim') {
                $date = $this->params()->fromRoute('date', 0);
                $this->getDayNotWorkedTable()->deleteDayNotWorked($date);
            }

            // Redirect to list of days

            $year_month = substr($date,0,6);
            $days_not_worked = $this->getDayNotWorkedTable()->fetchAllByMonth($year_month);
            
            $year = substr($year_month, 0, 4);
            $month = substr($year_month, 4, 2);

            $viewModel = new ViewModel(array(
                'days_not_worked' => $days_not_worked,
                'year' => $year,
                'month' => $month,
                'formated_date' => date_format(date_create($date), 'd/m/Y'),
                'date' => $date,
            ));

            return $viewModel->setTemplate('point/day-not-worked/index.phtml');

        }

        return array(
            'date'   => $date,
            'formated_date' => date_format(date_create($date), 'd/m/Y'),
            'day_not_worked' => $this->getDayNotWorkedTable()->getDayNotWorked($date)
        );
    }


    public function getDayNotWorkedTable()
    {
        if (!$this->dayNotWorkedTable) {
            $sm = $this->getServiceLocator();
            $this->dayNotWorkedTable = $sm->get('Point\Model\DayNotWorkedTable');
        }
        
        return $this->dayNotWorkedTable;
    }


}