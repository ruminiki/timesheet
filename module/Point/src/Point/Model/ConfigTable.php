<?php
namespace Point\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use DateTime;
use Point\Model\Config;

class ConfigTable
{
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $sql =  "SELECT ".
                    "c.id as id, c._key as _key, c.value as value  ".
               "FROM config c ";

        $statement = $this->tableGateway->adapter->query($sql); 
        return $statement->execute();
    }

    public function getConfig($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getValueByKey($key)
    {
        $rowset = $this->tableGateway->select(array('_key' => $key));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $key");
        }
        return $row->value;
    }

    public function saveConfig(Config $config)
    {
        $data = array(
            '_key' => $config->_key,
            'value'  => $config->value,
        );

        $id = (int) $config->id;
    
        if ($this->getConfig($id)) {
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }

    }

    public function deleteConfig($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }

}