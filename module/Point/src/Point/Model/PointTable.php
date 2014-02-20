<?php
namespace Point\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use DateTime;
use Point\Model\Point;

class PointTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->order('date ASC');
            $select->order('schedule ASC');
        });

        return $resultSet;
    }

    public function fetchAllByDay($_date)
    {
        $resultSet = $this->tableGateway->select(
            function (Select $select) use ($_date) {
                $select->where->equalTo('date', $_date);
                $select->order('schedule ASC');
            }
        ); 

        return $resultSet;
    }

    public function fetchAllByMonth($year_month)
    {
        //year_month formato Ym   
        $sql =  "SELECT ".
                    "p.date AS date, p.note as note,  ".
                    "GROUP_CONCAT(schedule ORDER BY schedule ASC SEPARATOR ' -- ') AS schedule, ".
                    "w.hours as worked_hours_day ".
                "FROM point p INNER JOIN worked_hours w on w.date = p.date and substring(w.date,1,6) = '" . $year_month . "' " .
                "GROUP BY p.date";

        $statement = $this->tableGateway->adapter->query($sql); 
        $points = $statement->execute();

        $month = substr($year_month, 4,2);
        $date = new DateTime( $year_month.'01' );
        $result = array();

        $f = fopen('/tmp/log.txt', 'w');
        
        //fwrite($f,'D: ' . $date->format( 'Ymd' ));
        $points->buffer();
        foreach ($points as $point) {
            
            while ( $date->format('m') == $month ){

                if ( $date->format( 'Ymd' ) == $point['date'] ){
                    
                    $p = new Point();
                    $p->date = $point['date'];
                    $p->schedule = $point['schedule'];
                    $p->note = $point['note'];
                    $p->worked_hours_day = $point['worked_hours_day'];
                    $p->day_of_week = $date->format('D');

                    array_push($result, $p);
                    $date->modify( 'next day' );

                    break;

                }else{

                    $p = new Point();
                    $p->date = $date->format( 'Ymd' );
                    $p->schedule = "";
                    $p->note = "";
                    $p->worked_hours_day = "";
                    $p->day_of_week = $date->format('D');
                    array_push($result, $p); 

                }

                $date->modify( 'next day' );

            }
            
        }
        //caso não tenham marcacoes até o final do mes, preenche o restante
        if ( $date->format('m') == $month ){
             while ( $date->format('m') == $month ){

                $p = new Point();
                $p->date = $date->format( 'Ymd' );
                $p->schedule = "";
                $p->note = "";
                $p->worked_hours_day = "";

                array_push($result, $p); 

                $date->modify( 'next day' );

            }
        }

        fclose($f);

        return $result;

    }

    public function getPoint($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function savePoint(Point $point)
    {
        $data = array(
            'date' => $point->date,
            'schedule'  => $point->schedule,
            'sequence'  => $point->sequence,
            'note'  => $point->note,
        );

        $id = (int)$point->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPoint($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deletePoint($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}