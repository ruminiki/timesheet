<?php
namespace Point\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Point\Form\ConfigForm;
use Point\Model\Config;
use Zend\Session\Container;
use DateTime;

class ConfigController extends AbstractActionController
{
    protected $configTable;
        
    public function indexAction()
    {

        return new ViewModel(array(
            'configurations' => $this->getConfigTable()->fetchAll(),
        ));

    }

    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $config = new Config();

            $config->id = $request->getPost("id");
            $config->_key = $request->getPost("key");

            if ( $config->_key == Config::DIAS_TRABALHADOS ){
                $config->value = $request->getPost("seg") . " " . $request->getPost("ter") . " " . $request->getPost("qua") . " " . 
                                 $request->getPost("qui") . " " . $request->getPost("sex") . " " . $request->getPost("sab") . " " . 
                                 $request->getPost("dom"); 
                //$config->value = preg_replace('/\s+/', '', $config->value);
            }else{
                $config->value = $request->getPost("value");
            }

            $this->getConfigTable()->saveConfig($config);

        }

        $viewModel = new ViewModel(array(
            'configurations' => $this->getConfigTable()->fetchAll(),
        ));

        return $viewModel->setTemplate('point/config/index.phtml');

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