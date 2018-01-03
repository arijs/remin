<?php

namespace App\Model;

class DesignacaoSaida
{
    public $designacao_id;
    public $saida_id;
    public $desa_comentario;
    public $designacao;
    public $saida;

    public function exchangeArray(array $data)
    {
        $this->designacao_id = !empty($data['designacao_id']) ? $data['designacao_id'] : null;
        $this->saida_id      = !empty($data['saida_id']) ? $data['saida_id'] : null;
        $this->desa_comentario = !empty($data['desa_comentario']) ? $data['desa_comentario'] : null;
    }

    public function toArray() {
        $data = [
            'designacao_id' => $this->designacao_id,
            'saida_id'      => $this->saida_id,
            'desa_comentario' => $this->desa_comentario,
        ];
        return $data;
    }

    public function setDesignacao(Designacao $designacao) {
        $this->designacao = $designacao;
    }

    public function setSaida(Saida $saida) {
        $this->saida = $saida;
    }
}
