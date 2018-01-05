<?php

namespace App\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;

class DesignacaoIrmaoTable
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

    public function fetchDesignacaoIrmaos(Designacao $designacao, $irmaosMap) {
        $designacao_id = (int) $designacao->designacao_id;
        $list = [];
        $rowset = $this->tableGateway->select([
            'designacao_id' => $designacao_id,
        ]);
        foreach ($rowset as $row) {
            $irmao = $irmaosMap[$row->irmao_id];
            if ($irmao instanceof Irmao) {
                $row->setIrmao($irmao);
            }
            $list[] = $row;
        }
        $designacao->setDesignacaoIrmaos($list);
    }

    public function getDesignacaoIrmao($designacao_id, $irmao_id)
    {
        $designacao_id = (int) $designacao_id;
        $irmao_id = (int) $irmao_id;
        $rowset = $this->tableGateway->select([
            'designacao_id' => $designacao_id,
            'irmao_id' => $irmao_id,
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

    public function insertDesignacaoIrmao(DesignacaoIrmao $designacaoIrmao)
    {
        $this->tableGateway->insert($designacaoIrmao->toArray());
    }

    public function insertFromArrays(Designacao $designacao, $arrayId, $arrayComentario)
    {
        $designacaoIrmaos = [];
        $indexIrmao = 0;
        $irmaos_id = !empty($arrayId) ? $arrayId : [];
        foreach ($irmaos_id as $irmao_id) {
            $designacaoIrmao = new DesignacaoIrmao();
            $designacaoIrmao->designacao_id = $designacao->designacao_id;
            $designacaoIrmao->irmao_id = $irmao_id;
            $designacaoIrmao->deir_comentario = !empty($arrayComentario[$indexIrmao]) ? $arrayComentario[$indexIrmao] : '';
            $this->insertDesignacaoIrmao($designacaoIrmao);
            $designacaoIrmaos[] = $designacaoIrmao;
            $indexIrmao++;
        }
        $designacao->setDesignacaoIrmaos($designacaoIrmaos);
    }

    public function updateDesignacaoIrmao(DesignacaoIrmao $designacaoIrmao)
    {
        $this->tableGateway->update(
            $designacaoIrmao->toArray(),
            [
                'designacao_id' => (int) $designacaoIrmao->designacao_id,
                'irmao_id' => (int) $designacaoIrmao->irmao_id,
            ]
        );
    }

    public function deleteDesignacaoIrmao($designacao_id, $irmao_id)
    {
        $this->tableGateway->delete([
            'designacao_id' => (int) $designacao_id,
            'irmao_id' => (int) $irmao_id,
        ]);
    }

    public function deleteDesignacao($designacao_id)
    {
        $this->tableGateway->delete([
            'designacao_id' => (int) $designacao_id,
        ]);
    }
}
