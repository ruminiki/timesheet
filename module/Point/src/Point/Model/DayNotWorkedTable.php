<?php
namespace Point\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class DayNotWorkedTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function markDayAsNotWorked($date, $reason)
    {
        $data = array(
            'date' => $date,
            'reason'  => $reason,
        );
        $this->tableGateway->delete($data);
        $this->tableGateway->insert($data);
    }

    public function getDayNotWorked($date)
    {
        $rowset = $this->tableGateway->select(array('date' => $date));
        $row = $rowset->current();
        if (!$row) {
            return "";    
        }
        return $row;
    }

    public function fetchAllByMonth($year_month)
    {

        $sql = "select date, reason from day_not_worked where substring(date,1,6) = '".$year_month."'";
        $statement = $this->tableGateway->adapter->query($sql); 

        $rowSet = $statement->execute();

        return $rowSet;

    }
    /**
    * Retorna um array com todos os dias (formato date (d)) não trabalhados no mês
    **/
    public function fetchAllByMonthAsArrayDayString($year_month)
    {
        
        $sql = "select substring(date,7,2) as day from day_not_worked where substring(date,1,6) = '".$year_month."'";
        $statement = $this->tableGateway->adapter->query($sql); 

        $rowSet = $statement->execute();
        $days = array();
        foreach ( $rowSet as $row ){
            array_push($days, $row['day']);
        }

        return $days;

    }

    public function deleteDayNotWorked($date)
    {
        $this->tableGateway->delete(array('date' => $date));
    }

}