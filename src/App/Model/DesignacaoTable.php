<?php

namespace App\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;

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
        $rowset = $this->fetchOffsetLimit($offset, $limit);
        $list = [];
        foreach ($rowset as $row) {
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
}
