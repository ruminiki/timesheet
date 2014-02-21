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
        $date = $this->params()->fromRoute('date', 0);
        $year_month = substr($date,0,6);
        $days_not_worked = $this->getDayNotWorkedTable()->fetchAllByMonth($year_month);
              
        return new ViewModel(array(
            'days_not_worked' => $days_not_worked,
            'formated_date' => date_format(date_create($date), 'd/m/Y'),
            'date' => $date,
        ));

    }

    public function markDayAsNotWorkedAction(){

        $date = $this->params()->fromRoute('date', 0);

        if (!$date) {
            return $this->redirect()->toRoute('point');
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $option = $request->getPost('option', 'Cancel');

            if ($option == 'Save') {
                //verify if user was mark a period    
                $end_date_period = $request->getPost('datepicker-end-period-not-worked');
                if ( !empty($end_date_period) ){

                    $start_date = new DateTime( $request->getPost('datepicker-start-period-not-worked') );
                    $end_date   = DateTime::createFromFormat( "d/m/Y", $end_date_period );

                    while ($start_date <= $end_date){
                        $reason = $request->getPost('reason');
                        $this->getDayNotWorkedTable()->markDayAsNotWorked($start_date->format("Ymd"), $reason);
                        $start_date->modify( 'next day' );
                    }
                }else{
                    $reason = $request->getPost('reason');
                    $this->getDayNotWorkedTable()->markDayAsNotWorked($date, $reason);
                }    
            }

            // Redirect to list of points
            return $this->redirect()->toRoute('point');
        }
        
        return array(
            'date' => $date,
            'formated_date' => date_format(date_create($date), 'd/m/Y'),
        );
    }

    public function deleteAction()
    {
        $date = $this->params()->fromRoute('date', 0);
        if (!$date) {
            return $this->redirect()->toRoute('day-not-worked');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $date = $this->params()->fromRoute('date', 0);
                $this->getDayNotWorkedTable()->deleteDayNotWorked($date);
            }

            // Redirect to list of days

            $year_month = substr($date,0,6);
            $days_not_worked = $this->getDayNotWorkedTable()->fetchAllByMonth($year_month);
                  
            $viewModel = new ViewModel(array(
                'days_not_worked' => $days_not_worked,
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