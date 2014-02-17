<?php
namespace Point\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Point\Form\PointForm;
use Point\Model\Point;
use Zend\Session\Container;
use DateTime;

class PointController extends AbstractActionController
{
    protected $pointTable;
    protected $workedHoursTable;
        
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
            'worked_hours' => $this->getWorkedHoursTable()->getWorkedHours($container->selectedDate),
            //'points' => $this->getPointTable()->fetchAll(),
        ));

    }

    public function nextMonthAction(){

        $container = new Container('selectedDate');
        $date = date_create($container->selectedDate);
        $day = date_format($date, 'd');
        $month = date_format($date, 'm');
        $year = date_format($date, 'Y');

        if ( $month == '12'){
            $month = '01';
            $year = intval($year)+1;
            $container->selectedDate = $year.$month.$day;
        }else{
            $month = intval($month) + 1;
            if ( $month < 10 ){
                $month = '0'.$month;
            }
            $container->selectedDate = $year.$month.$day;
        }

        $month_label = substr(date("F", strtotime($container->selectedDate)), 0, 3);

        // Redirect to list of points
         $viewModel = new ViewModel(array(
            'points' => $this->getPointTable()->fetchAllByDay($container->selectedDate),
            'month' => $month,
            'year' => $year,
            'day' => $day,
            'month_label' => $month_label,
            'worked_hours' => $this->getWorkedHoursTable()->getWorkedHours($container->selectedDate),
            'selected_date' => $day."/".$month."/".$year,
        ));

        return $viewModel->setTemplate('point/point/index.phtml');
    }

    public function previousMonthAction(){

        $container = new Container('selectedDate');
        $date = date_create($container->selectedDate);
        $day = date_format($date, 'd');
        $month = date_format($date, 'm');
        $year = date_format($date, 'Y');

        if ( $month == '01'){
            $month = '12';
            $year = intval($year)-1;
            $container->selectedDate = $year.$month.$day;
        }else{
            $month = intval($month) - 1;
            if ( $month < 10 ){
                $month = '0'.$month;
            }
            $container->selectedDate = $year.$month.$day;
        }
        
        $month_label = substr(date("F", strtotime($container->selectedDate)), 0, 3);
        
        // Redirect to list of points
         $viewModel = new ViewModel(array(
            'points' => $this->getPointTable()->fetchAllByDay($container->selectedDate),
            'month' => $month,
            'year' => $year,
            'day' => $day,
            'month_label' => $month_label,
            'worked_hours' => $this->getWorkedHoursTable()->getWorkedHours($container->selectedDate),
            'selected_date' => $day."/".$month."/".$year,
        ));

        return $viewModel->setTemplate('point/point/index.phtml');
    }

    public function nextYearAction(){

        $container = new Container('selectedDate');
        $date = date_create($container->selectedDate);
        $day = date_format($date, 'd');
        $month = date_format($date, 'm');
        $year = date_format($date, 'Y');
        $month_label = substr(date("F", strtotime($container->selectedDate)), 0, 3);

        $year = intval($year) + 1;
        $container->selectedDate = $year.$month.$day;

        // Redirect to list of points
         $viewModel = new ViewModel(array(
            'points' => $this->getPointTable()->fetchAllByDay($container->selectedDate),
            'month' => $month,
            'year' => $year,
            'day' => $day,
            'month_label' => $month_label,
            'worked_hours' => $this->getWorkedHoursTable()->getWorkedHours($container->selectedDate),
            'selected_date' => $day."/".$month."/".$year,
        ));

        return $viewModel->setTemplate('point/point/index.phtml');
    }

    public function previousYearAction(){
        $container = new Container('selectedDate');
        $date = date_create($container->selectedDate);
        $day = date_format($date, 'd');
        $month = date_format($date, 'm');
        $year = date_format($date, 'Y');
        $month_label = substr(date("F", strtotime($container->selectedDate)), 0, 3);

        $year = intval($year) - 1;
        $container->selectedDate = $year.$month.$day;

        // Redirect to list of points
         $viewModel = new ViewModel(array(
            'points' => $this->getPointTable()->fetchAllByDay($container->selectedDate),
            'month' => $month,
            'year' => $year,
            'day' => $day,
            'month_label' => $month_label,
            'worked_hours' => $this->getWorkedHoursTable()->getWorkedHours($container->selectedDate),
            'selected_date' => $day."/".$month."/".$year,
        ));

        return $viewModel->setTemplate('point/point/index.phtml');
    }

    public function fetchByDayAction(){
        
        $date = $this->date = $this->params()->fromRoute('date', 0);

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
            'worked_hours' => $this->getWorkedHoursTable()->getWorkedHours($container->selectedDate),
            'selected_date' => $day."/".$month."/".$year,
        ));

        return $viewModel->setTemplate('point/point/index.phtml');

    }

    public function addAction()
    {
        
        //$date = $this->date = $this->params()->fromRoute('date', 0);

        $form = new PointForm();
        $form->get('submit')->setValue('Salvar');
        //retrieve selected date from session
        $container = new Container('selectedDate');
        //format date (Ymd) to (d/m/Y)
        $date = date_create($container->selectedDate);

        $form->get('date')->setValue(date_format($date, 'd/m/Y'));

        $request = $this->getRequest();
        if ($request->isPost()) {
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
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($point->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getPointTable()->savePoint($form->getData());

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

    public function calculateWorkedHours($points, $date){

        $worked_hours_interval = array();
        $worked_hours = "";
        $point_aux = null;

        $index = 1;
        $points->buffer();

        //$f = fopen("/tmp/time.log", "w");

        foreach ($points as $point){
            
            if ( $index % 2 == 0 ){
                
                $a = new DateTime($point_aux->schedule);
                $b = new DateTime($point->schedule);

                $interval = $a->diff($b);

                //fwrite($f, $point->schedule . " - " . $point_aux->schedule . '\n');
                //fwrite($f, 'P: ' . $interval->format("%H:%I:%S") . '     ');

                array_push($worked_hours_interval, $interval->format("%H:%I:%S"));    

            }else{
                $point_aux = $point;
            }

            $index++;

        }    

        //fwrite($f, implode(",", $worked_hours_interval));
        //fwrite($f, count($worked_hours_interval));

        

        for ($i = 0; $i < count($worked_hours_interval); $i++){
            if ( $i == 0 ){
                $worked_hours = $worked_hours_interval[$i];
            }else{
                $midnight = strtotime("0:00");
                
                $t1 = strtotime($worked_hours_interval[$i]) - $midnight;
                $t2 = strtotime($worked_hours) - $midnight;

                $seconds = $t1 + $t2;

                //$worked_hours = date("H:i:s",$seconds);
                $worked_hours = date("H:i", $midnight + $seconds);
            }
            //fwrite($f, 'W: ' . $worked_hours . "    ");
        }
        
        //fclose($f);
        $this->getWorkedHoursTable()->saveWorkedHours($date, $worked_hours);

    }

}