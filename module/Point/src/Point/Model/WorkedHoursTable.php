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

    public function getSumWorkedHoursMonth($month)
    {
        //$rowset = $this->tableGateway->select(array('date' => $month));
        /*$rowSet = $this->tableGateway->select()->from('worked_hours', array('sum(hours) as total'))->where("substring(date,5,2) = '" . $month . "'");
        $row = $rowSet->current();
        return $row->total; */

        $sql = "select SEC_TO_TIME(SUM(TIME_TO_SEC(hours))) as total from worked_hours where substring(date,5,2) = '".$month."'";
        $statement = $this->tableGateway->adapter->query($sql); 

        $rowSet = $statement->execute();
        $row = $rowSet->current();

        return $row['total'];

        
        
        /*
        $result = $this->tableGateway->fetchAll($sql);
        $row = $result->current();
        return $row->total;
        */


        /*$sql = $this->tableGateway->getSql();

        // We'll follow the regular order of SQL ( SELECT, FROM, WHERE )
        // So the query is easier to understand
        $select = $sql->select()
        // Use an alias as key in the columns array instead of
        // in the expression itself
        ->columns(array('total' => new \Zend\Db\Sql\Expression('SUM(hours)')))
        // Type casting the variables as integer can take place
        // here ( it even tells us a little about the table structure )
        ->where(array("substring(date,5,2)" => $month));

        // Use selectWith as a shortcut to get a resultSet for the above select
        $rowSet = $this->tableGateway->selectWith($select);
        $row = $rowSet->current();
        return $row->total; */

    }

}