<?php

namespace App\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;

class DesignacaoSaidaTable
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

    public function fetchDesignacaoSaidas(Designacao $designacao, $saidasMap) {
        $designacao_id = (int) $designacao->designacao_id;
        $list = [];
        $rowset = $this->tableGateway->select([
            'designacao_id' => $designacao_id,
        ]);
        foreach ($rowset as $row) {
            $saida = $saidasMap[$row->saida_id];
            if ($saida instanceof Saida) {
                $row->setSaida($saida);
            }
            $list[] = $row;
        }
        $designacao->setDesignacaoSaidas($list);
    }

    public function getDesignacaoSaida($designacao_id, $saida_id)
    {
        $designacao_id = (int) $designacao_id;
        $saida_id = (int) $saida_id;
        $rowset = $this->tableGateway->select([
            'designacao_id' => $designacao_id,
            'saida_id' => $saida_id,
        ]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d, %d',
                $designacao_id,
                $irmao_id
            ));
        }

        return $row;
    }

    public function insertDesignacaoSaida(DesignacaoSaida $designacaoSaida)
    {
        $this->tableGateway->insert($designacaoSaida->toArray());
    }

    public function insertFromArrays(Designacao $designacao, $arrayId, $arrayComentario)
    {
        $designacaoSaidas = [];
        $indexSaida = 0;
        $saidas_id = !empty($arrayId) ? $arrayId : [];
        foreach ($saidas_id as $saida_id) {
            $designacaoSaida = new DesignacaoSaida();
            $designacaoSaida->designacao_id = $designacao->designacao_id;
            $designacaoSaida->saida_id = $saida_id;
            $designacaoSaida->desa_comentario = $arrayComentario[$indexSaida];
            $this->insertDesignacaoSaida($designacaoSaida);
            $designacaoSaidas[] = $designacaoSaida;
            $indexSaida++;
        }
        $designacao->setDesignacaoSaidas($designacaoSaidas);
    }

    public function updateDesignacaoSaida(DesignacaoSaida $designacaoSaida)
    {
        $this->tableGateway->update(
            $designacaoSaida->toArray(),
            [
                'designacao_id' => (int) $designacaoSaida->designacao_id,
                'saida_id' => (int) $designacaoSaida->saida_id,
            ]
        );
    }

    public function deleteDesignacaoSaida($designacao_id, $saida_id)
    {
        $this->tableGateway->delete([
            'designacao_id' => (int) $designacao_id,
            'saida_id' => (int) $saida_id,
        ]);
    }

    public function deleteDesignacao($designacao_id)
    {
        $this->tableGateway->delete([
            'designacao_id' => (int) $designacao_id,
        ]);
    }
}
