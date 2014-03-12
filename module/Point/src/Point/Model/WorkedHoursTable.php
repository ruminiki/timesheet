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

    public function getSumWorkedHoursMonth($year_month)
    {
        $sql = "select COALESCE(TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(hours))), '%H:%i'),'00:00') as total from worked_hours where substring(date,1,6) = '".$year_month."'";
        $statement = $this->tableGateway->adapter->query($sql); 

        $rowSet = $statement->execute();
        $row = $rowSet->current();

        return $row['total'];
    }

}