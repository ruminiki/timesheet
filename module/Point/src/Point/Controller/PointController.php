<?php
namespace Point\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Point\Form\PointForm;
use Point\Model\Point;
use Zend\Session\Container;

class PointController extends AbstractActionController
{
    protected $pointTable;
        
    public function indexAction()
    {
        
        $container = new Container('selectedDate');

        if ( empty($container->selectedDate) || is_null($container->selectedDate) ){
            $container->selectedDate = date('Y').date('m').date('d');
        }

        $date = date_create($container->selectedDate);

        return new ViewModel(array(
            'points' => $this->getPointTable()->fetchAllByDay($container->selectedDate),
            'month' => date_format($date,'m'),
            'year' => date_format($date,'Y'),
            'day' => date_format($date,'d'),
            'month_label' => substr(date("F", strtotime($container->selectedDate)), 0, 3),
            'selected_date' => date_format($date,'d')."/".date_format($date,'m')."/".date_format($date,'Y'),
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

        // Redirect to list of points
         $viewModel = new ViewModel(array(
            'points' => $this->getPointTable()->fetchAllByDay($container->selectedDate),
            'month' => $month,
            'year' => $year,
            'day' => $day,
            'month_label' => $month_label,
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

}