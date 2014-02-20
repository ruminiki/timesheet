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
        $this->tableGateway->insert($data);
    }

    public function getDayNotWorkedMonth($year_month)
    {
        $sql = "select * from day_not_worked where substring(date,1,6) = '".$year_month."'";
        $statement = $this->tableGateway->adapter->query($sql); 

        $rowSet = $statement->execute();
        return $rowSet;
    }

}