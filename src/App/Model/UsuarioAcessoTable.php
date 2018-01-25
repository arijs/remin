<?php

namespace App\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;

class UsuarioAcessoTable
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

    public function insertUsuarioAcesso(UsuarioAcesso $usuarioAcesso)
    {
        $this->tableGateway->insert($usuarioAcesso->toArray());
    }

    public function updateUsuarioAcesso(UsuarioAcesso $usuarioAcesso)
    {
        $this->tableGateway->update(
            $usuarioAcesso->toArray(),
            [
                'usuario_id' => (int) $usuarioAcesso->usuario_id,
                'acesso_data' => $usuarioAcesso->acesso_data,
            ]
        );
    }

    public function deleteUsuario($id)
    {
        $this->tableGateway->delete(['usuario_id' => (int) $id]);
    }
}
