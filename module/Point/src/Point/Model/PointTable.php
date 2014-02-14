<?php
namespace Point\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

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
            $select->order('sequence ASC');
        });

        return $resultSet;
    }

    public function fetchAllByDay($_date)
    {
        $resultSet = $this->tableGateway->select(
            function (Select $select) use ($_date) {
                $select->where->equalTo('date', $_date);
                $select->order('sequence ASC');
            }
        ); 

        return $resultSet;
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