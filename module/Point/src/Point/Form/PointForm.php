<?php
namespace Point\Form;

use Zend\Form\Form;

class PointForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('point');

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'date',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'date',
                'readonly' => TRUE,
            ),
            'options' => array(
                'label' => 'Data',
                'readonly' => 'readonly',
            ),
        ));
        $this->add(array(
            'name' => 'schedule',
            'type' => 'Text',
            'options' => array(
                'label' => 'Hora',
            ),
        ));
        $this->add(array(
            'name' => 'sequence',
            'type' => 'Text',
            'options' => array(
                'label' => 'Sequência',
            ),
        ));
        
        $this->add(array(
            'name' => 'note',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Notas',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'button',
            'attributes' => array(
                'type' => 'submit', 
                'class' => 'btn btn-primary',
                'id' => 'submitbutton'),
            'options' => array('label' => 'Submit'),
        ));

    }
}
