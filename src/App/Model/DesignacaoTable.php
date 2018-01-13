<?php

namespace App\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class DesignacaoTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchOffsetLimit($offset, $limit)
    {
        return $this->tableGateway->select(function (Select $select) use ($offset, $limit) {
            $select->offset($offset)->limit($limit);
        });
    }

    public function fetchOffsetLimitArray($offset, $limit)
    {
        $result = $this->fetchOffsetLimit($offset, $limit);
        return $this->resultToArray($result);
    }

    public function resultToArray($result)
    {
        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }
        return $list;
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

    public function fetchDesignacaoIrmaos($designacao) {}

    public function getDesignacao($designacao_id)
    {
        $designacao_id = (int) $designacao_id;
        $rowset = $this->tableGateway->select([
            'designacao_id' => $designacao_id,
        ]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $designacao_id
            ));
        }

        return $row;
    }

    public function saveDesignacao(Designacao $designacao)
    {
        $id = (int) $designacao->designacao_id;

        if ($id === 0) {
            return $this->insertDesignacao($designacao);
        }

        if (! $this->getDesignacao($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update designacao with identifier %d; does not exist',
                $id
            ));
        }

        return $this->updateDesignacao($designacao);
    }

    public function insertDesignacao(Designacao $designacao)
    {
        $array = $designacao->toArray();
        if (empty($array['designacao_entrega'])) {
            $array['designacao_entrega'] = '0000-00-00';
        }
        if (empty($array['designacao_devolucao'])) {
            $array['designacao_devolucao'] = '0000-00-00';
        }
        $this->tableGateway->insert($array);
        $designacao->designacao_id = $this->tableGateway->getLastInsertValue();
    }

    public function updateDesignacao(Designacao $designacao)
    {
        $this->tableGateway->update(
            $designacao->toArray(),
            [
                'designacao_id' => (int) $designacao->designacao_id,
            ]
        );
    }

    public function deleteDesignacao($designacao_id)
    {
        $this->tableGateway->delete([
            'designacao_id' => (int) $designacao_id,
        ]);
    }

    public function getTerritoriosRanked()
    {
        $adapter = $this->tableGateway->getAdapter();
        $rs = new ResultSet();
        $rs->setArrayObjectPrototype(new Designacao());
        $adapter->query('SET @terr_current = 0', Adapter::QUERY_MODE_EXECUTE);
        $adapter->query('SET @terr_rank = 0', Adapter::QUERY_MODE_EXECUTE);
        $result = $adapter->query(
            'SELECT designacao_id
            , designacao_territorio
            , designacao_entrega
            , designacao_devolucao
            , designacao_comentario
            , @terr_rank := IF(@terr_current = designacao_territorio, @terr_rank + 1, 1) AS terr_rank
            , @terr_current := designacao_territorio as terr_current
            FROM designacoes
            ORDER BY designacao_territorio ASC
            , designacao_entrega DESC
            , designacao_id DESC',
            Adapter::QUERY_MODE_EXECUTE,
            $rs
        );
        return $this->resultToArray($result);
    }

    public function getTerritoriosByIrmaoRanked($irmao_id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $rs = new ResultSet();
        $rs->setArrayObjectPrototype(new Designacao());
        $adapter->query('SET @terr_current = 0', Adapter::QUERY_MODE_EXECUTE);
        $adapter->query('SET @terr_rank = 0', Adapter::QUERY_MODE_EXECUTE);
        $result = $adapter->query(
            'SELECT d.designacao_id
            , d.designacao_territorio
            , d.designacao_entrega
            , d.designacao_devolucao
            , d.designacao_comentario
            , @terr_rank := IF(@terr_current = d.designacao_territorio, @terr_rank + 1, 1) AS terr_rank
            , @terr_current := d.designacao_territorio as terr_current
            FROM designacoes_irmaos di
            RIGHT JOIN designacoes d
            ON di.designacao_id = d.designacao_id
            WHERE di.irmao_id = ?
            ORDER BY d.designacao_territorio ASC
            , d.designacao_entrega DESC
            , d.designacao_id DESC',
            [$irmao_id],
            $rs
        );
        return $this->resultToArray($result);
    }

    public function resultGroupByTerritorio($result)
    {
        $groupList = [];
        $groupMap = [];
        $group = null;
        foreach ($result as $desig) {
            $dt = $desig->designacao_territorio;
            if (empty($groupMap[$dt])) {
                $groupMap[$dt] = [
                    'territorio' => $dt,
                    'list' => []
                ];
                $group = &$groupMap[$dt];
                $groupList[] = &$group;
            } else {
                $group = &$groupMap[$dt];
            }
            $group['list'][] = $desig;
        }
        return $groupList;
    }
}
