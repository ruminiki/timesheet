<?php
namespace Point\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Point\Form\PointForm;
use Point\Model\Point;
use Zend\Session\Container;
use DateTime;
use DateTimeZone;

class PointController extends AbstractActionController
{
    protected $pointTable;
    protected $workedHoursTable;
    protected $dayNotWorkedTable;
        
    public function indexAction()
    {
        $container = new Container('selectedDate');

        if ( empty($container->selectedDate) || is_null($container->selectedDate) ){
            $container->selectedDate = date('Y').date('m').date('d');
        }

        $date = date_create($container->selectedDate);
        $points = $this->getPointTable()->fetchAllByDay($container->selectedDate);
        $this->calculateWorkedHours($points, $container->selectedDate);
       
        return new ViewModel(array(
            'points' => $points,
            'month' => date_format($date,'m'),
            'year' => date_format($date,'Y'),
            'day' => date_format($date,'d'),
            'month_label' => substr(date("F", strtotime($container->selectedDate)), 0, 3),
            'selected_date' => date_format($date,'d')."/".date_format($date,'m')."/".date_format($date,'Y'),
            'worked_hours_day' => $this->getWorkedHoursTable()->getWorkedHours($container->selectedDate),
            'worked_hours_month' => $this->getWorkedHoursTable()->getSumWorkedHoursMonth(date_format($date,'Y').date_format($date,'m')),
            'days_not_worked' => $this->getDayNotWorkedTable()->fetchAllByMonthAsArrayDayString(date_format($date,'Y').date_format($date,'m')),
        ));

    }

    public function changeMonthAction()
    {

        $param = $this->date = $this->params()->fromRoute('date', 0);

        $container = new Container('selectedDate');
        $date = date_create($container->selectedDate);

        $day = date_format($date, 'd');
        //atualiza a data na sessao
        $container->selectedDate = $param.$day;

        //---------------------
        //prepara para carregar os registros e retornar para a view
        $date = date_create($container->selectedDate);
        $day = date_format($date, 'd');
        $month = date_format($date, 'm');
        $year = date_format($date, 'Y');
        $month_label = substr(date("F", strtotime($container->selectedDate)), 0, 3);

        // Redirect to list of points
        $viewModel = new ViewModel(array(
            'points' => $this->getPointTable()->fetchAllByDay($container->selectedDate),
            'month' => $month,
            'year' => $year,
            'day' => $day,
            'month_label' => $month_label,
            'worked_hours_day' => $this->getWorkedHoursTable()->getWorkedHours($container->selectedDate),
            'worked_hours_month' => $this->getWorkedHoursTable()->getSumWorkedHoursMonth($year.$month),
            'days_not_worked' => $this->getDayNotWorkedTable()->fetchAllByMonthAsArrayDayString($year.$month),
            'selected_date' => $day."/".$month."/".$year,
        ));

        return $viewModel->setTemplate('point/point/index.phtml');
    }

    public function fetchByDayAction()
    {
        
        $date = $this->params()->fromRoute('date', 0);

        $container = new Container('selectedDate');
        $container->selectedDate = $date;
        
        $month = substr($date, 4, 2);
        $year = substr($date, 0, 4);
        $day = substr($date, 6, 2);

        $month_label = substr(date("F", strtotime($date)), 0, 3);
        $points = $this->getPointTable()->fetchAllByDay($container->selectedDate);
        $this->calculateWorkedHours($points, $container->selectedDate);

        // Redirect to list of points
         $viewModel = new ViewModel(array(
            'points' => $points,
            'month' => $month,
            'year' => $year,
            'day' => $day,
            'month_label' => $month_label,
            'selected_date' => $day."/".$month."/".$year,
            'worked_hours_day' => $this->getWorkedHoursTable()->getWorkedHours($container->selectedDate),
            'worked_hours_month' => $this->getWorkedHoursTable()->getSumWorkedHoursMonth($year.$month),
            'days_not_worked' => $this->getDayNotWorkedTable()->fetchAllByMonthAsArrayDayString($year.$month),
        ));

        return $viewModel->setTemplate('point/point/index.phtml');

    }

    public function addInLineAction(){
        $request = $this->getRequest();

        //retrieve selected date from session
        $container = new Container('selectedDate');
        //format date (Ymd) to (d/m/Y)
        $date = date_create($container->selectedDate);
        $this->getPointTable()->deletePointByDate(date_format($date, 'Ymd'));

        if ($request->isPost()) {

            for ( $i = 1; $i <= 4; $i++ ){

                $point = new Point();
                $point->schedule = $request->getPost('h'.$i);
                $point->date = date_format($date, 'Ymd');
                $this->getPointTable()->savePoint($point);
                
            }

        }
        return $this->redirect()->toRoute('point');
    }

    public function addAction()
    {
        
        //$date = $this->date = $this->params()->fromRoute('date', 0);

        $form = new PointForm();
        
        //retrieve selected date from session
        $container = new Container('selectedDate');
        //format date (Ymd) to (d/m/Y)
        $date = date_create($container->selectedDate);

        $form->get('date')->setValue(date_format($date, 'd/m/Y'));

        $request = $this->getRequest();
        if ($request->isPost()) {

            $option = $request->getPost('option', 'Cancelar');

            if ($option == 'Salvar') {

                $point = new Point();
                $form->setInputFilter($point->getInputFilter());
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $point->exchangeArray($form->getData());
                    //return date format do save in database
                    $point->date = date_format($date, 'Ymd');
                    $this->getPointTable()->savePoint($point);

                    // Redirect to list of points
                    return $this->redirect()->toRoute('point');
                }
            }else{
                // Redirect to list of points
                return $this->redirect()->toRoute('point');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('point', array(
                'action' => 'add'
            ));
        }

        // Get the Point with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $point = $this->getPointTable()->getPoint($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('point', array(
                'action' => 'index'
            ));
        }

        $form  = new PointForm();
        $form->bind($point);

        $container = new Container('selectedDate');
        //format date (Ymd) to (d/m/Y)
        $date = date_create($container->selectedDate);

        $form->get('date')->setValue(date_format($date, 'd/m/Y'));

        $request = $this->getRequest();
        if ($request->isPost()) {

            $option = $request->getPost('option', 'Cancelar');

            if ($option == 'Salvar') {

                $form->setInputFilter($point->getInputFilter());
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $point = $form->getData();
                    //return date format do save in database
                    $point->date = date_format($date, 'Ymd');
                    $this->getPointTable()->savePoint($point);

                    // Redirect to list of points
                    return $this->redirect()->toRoute('point');
                }

            }else{
                // Redirect to list of points
                return $this->redirect()->toRoute('point');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('point');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getPointTable()->deletePoint($id);
            }

            // Redirect to list of points
            return $this->redirect()->toRoute('point');
        }

        return array(
            'id'    => $id,
            'point' => $this->getPointTable()->getPoint($id)
        );
    }

    ///===get tables
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

    public function calculateWorkedHours($points, $date){

        $worked_hours_interval = array();
        $worked_hours = "";
        $point_aux = null;

        $index = 1;
        $points->buffer();

        foreach ($points as $point){
            
            if ( $index % 2 == 0 ){
                
                $a = new DateTime($point_aux->schedule);
                $b = new DateTime($point->schedule);

                $interval = $a->diff($b);

                array_push($worked_hours_interval, $interval->format("%H:%I:%S"));    

            }else{
                $point_aux = $point;
            }

            $index++;

        }    

        for ($i = 0; $i < count($worked_hours_interval); $i++){
            if ( $i == 0 ){
                $worked_hours = $worked_hours_interval[$i];
            }else{
                $midnight = strtotime("0:00");
                
                $t1 = strtotime($worked_hours_interval[$i]) - $midnight;
                $t2 = strtotime($worked_hours) - $midnight;

                $seconds = $t1 + $t2;

                $worked_hours = date("H:i:s", $midnight + $seconds);
            }
        }
        
        $this->getWorkedHoursTable()->saveWorkedHours($date, $worked_hours);

    }

}