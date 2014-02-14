<?php
namespace Point\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Point\Form\PointForm;
use Point\Model\Point;

class PointController extends AbstractActionController
{
    protected $pointTable;
    
    public function indexAction()
    {
        /*$date = (date('Y').date('m').date('d'));
        return new ViewModel(array(
            'points' => $this->getPointTable()->fetchAllByDay($date),
        ));*/
    }

    public function fetchByDayAction()
    {
        
        $date = $this->date = $this->params()->fromRoute('date', 0);

        return array(
                'points' => $this->getPointTable()->fetchAllByDay($date),
        );

    }

    public function addAction()
    {
        
        //$date = $this->date = $this->params()->fromRoute('date', 0);

        $form = new PointForm();
        $form->get('submit')->setValue('Salvar');

        //preenche a data no campo date
        $form->get('date')->setValue($this->date);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $point = new Point();
            $form->setInputFilter($point->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $point->exchangeArray($form->getData());
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