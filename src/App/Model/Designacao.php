<?php

namespace App\Model;

class Designacao
{
    public $designacao_id;
    public $designacao_territorio;
    public $designacao_entrega;
    public $designacao_devolucao;
    public $designacao_comentario;
    public $designacaoIrmaos;
    public $designacaoSaidas;

    public function exchangeArray(array $data)
    {
        $this->designacao_id = !empty($data['designacao_id']) ? $data['designacao_id'] : null;
        $this->designacao_territorio = !empty($data['designacao_territorio']) ? $data['designacao_territorio'] : null;
        $this->designacao_entrega    = !empty($data['designacao_entrega']) ? $data['designacao_entrega'] : null;
        $this->designacao_devolucao  = !empty($data['designacao_devolucao']) ? $data['designacao_devolucao'] : null;
        $this->designacao_comentario = !empty($data['designacao_comentario']) ? $data['designacao_comentario'] : null;
    }

    public function toArray() {
        $data = [
            'designacao_id' => $this->designacao_id,
            'designacao_territorio' => $this->designacao_territorio,
            'designacao_entrega'    => $this->designacao_entrega,
            'designacao_devolucao'  => $this->designacao_devolucao,
            'designacao_comentario' => $this->designacao_comentario,
            // 'designacao_irmaos' => $this->getDesignacaoIrmaosArray(),
            // 'designacao_saidas' => $this->getDesignacaoSaidasArray(),
        ];
        return $data;
    }

    public function setDesignacaoIrmaos($designacaoIrmaos) {
        $this->designacaoIrmaos = $designacaoIrmaos;
    }

    public function getDesignacaoIrmaosArray() {
        $list = [];
        $deir = $this->designacaoIrmaos;
        if (is_array($deir)) {
            foreach ($deir as $deIrmao) {
                $list[] = $deIrmao->toArray();
            }
        }
        return $list;
    }

    public function setDesignacaoSaidas($designacaoSaidas) {
        $this->designacaoSaidas = $designacaoSaidas;
    }

    public function getDesignacaoSaidasArray() {
        $list = [];
        $desa = $this->designacaoSaidas;
        if (is_array($desa)) {
            foreach ($desa as $deSaida) {
                $list[] = $deSaida->toArray();
            }
        }
        return $list;
    }

}
