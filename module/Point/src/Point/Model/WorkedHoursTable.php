<?php
namespace Point\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class WorkedHoursTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function saveWorkedHours($date, $hours)
    {
        //remove para não haver duplicacao
        $this->deleteWorkedHours($date);

        $data = array(
            'date' => $date,
            'hours'  => $hours,
        );
        $this->tableGateway->insert($data);

    }

    public function deleteWorkedHours($date)
    {
        $this->tableGateway->delete(array('date' => $date));
    }

    public function getWorkedHours($date)
    {
        $rowset = $this->tableGateway->select(array('date' => $date));
        $row = $rowset->current();
        if (!$row) {
            return "";    
        }
        return $row->hours;
    }

}