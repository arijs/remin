<?php

namespace App\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;

class IrmaoTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function listToMap($list)
    {
        $map = [];
        foreach ($list as $irmao) {
            $map[$irmao->irmao_id] = $irmao;
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

    public function getIrmao($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['irmao_id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveIrmao(Irmao $irmao)
    {
        $id = (int) $irmao->irmao_id;

        if ($id === 0) {
            return $this->insertIrmao($irmao);
        }

        if (! $this->getIrmao($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update irmao with identifier %d; does not exist',
                $id
            ));
        }

        return $this->updateIrmao($irmao);
    }

    public function insertIrmao(Irmao $irmao)
    {
        $this->tableGateway->insert($irmao->toArray());
        $irmao->irmao_id = $this->tableGateway->getLastInsertValue();
    }

    public function updateIrmao(Irmao $irmao)
    {
        $this->tableGateway->update(
            $irmao->toArray(),
            ['irmao_id' => (int) $irmao->irmao_id]
        );
    }

    public function deleteIrmao($id)
    {
        $this->tableGateway->delete(['irmao_id' => (int) $id]);
    }
}
