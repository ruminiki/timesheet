<?php

namespace Point\Model;
// Add these import statements
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Config implements InputFilterAwareInterface 
{

    const JORNADA_TRABALHO_SEMANAL = "Jornada diária (hrs)";
    const SALDO_INICIAL_BANCO_DE_HORAS = "Saldo inicial banco de horas (hrs)";
    const JORNADA_SEMANAL = "Jornada semanal";

    public $id;
    public $_key;
    public $value;
    protected $inputFilter;    

    public function exchangeArray($data)
    {
        $this->_key = (isset($data['key'])) ? $data['key'] : null;
        $this->value  = (isset($data['value'])) ? $data['value'] : null;
    }

// Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'key',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }


}
