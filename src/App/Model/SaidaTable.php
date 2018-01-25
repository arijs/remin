<?php

namespace App\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;

class SaidaTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function listToMap($list)
    {
        $map = [];
        foreach ($list as $saida) {
            $map[$saida->saida_id] = $saida;
        }
        return $map;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchAllArray()
    {
        $rowset = $this->fetchAll();
        $list = [];
        foreach ($rowset as $row) {
            $list[] = $row;
        }
        return $list;
    }

    public function fetchOffsetLimit($offset, $limit)
    {
        return $this->tableGateway->select(function (Select $select) use ($offset, $limit) {
            $select->offset($offset)->limit($limit);
        });
    }

    public function countAll()
    {
        $sql = $this->tableGateway->getSql();
        $select = $sql->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $statement = $sql->prepareStatementForSqlObject($select);
        $rowset = $statement->execute();
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(
                'Could not count table rows'
            );
        }

        return $row;
    }

    public function getSaida($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['saida_id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveSaida(Saida $saida)
    {
        $id = (int) $saida->saida_id;

        if ($id === 0) {
            return $this->insertSaida($saida);
        }

        return $this->updateSaida($saida);
    }

    public function insertSaida(Saida $saida)
    {
        $this->tableGateway->insert($saida->toArray());
        $saida->saida_id = $this->tableGateway->getLastInsertValue();
    }

    public function updateSaida(Saida $saida)
    {
        $this->tableGateway->update(
            $saida->toArray(),
            ['saida_id' => (int) $saida->saida_id]
        );
    }

    public function deleteSaida($id)
    {
        $this->tableGateway->delete(['saida_id' => (int) $id]);
    }
}
