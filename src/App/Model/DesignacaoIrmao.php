<?php

namespace App\Model;

class DesignacaoIrmao
{
    public $designacao_id;
    public $irmao_id;
    public $deir_comentario;
    public $designacao;
    public $irmao;

    public function exchangeArray(array $data)
    {
        $this->designacao_id = !empty($data['designacao_id']) ? $data['designacao_id'] : null;
        $this->irmao_id      = !empty($data['irmao_id']) ? $data['irmao_id'] : null;
        $this->deir_comentario = !empty($data['deir_comentario']) ? $data['deir_comentario'] : null;
    }

    public function toArray() {
        $data = [
            'designacao_id' => $this->designacao_id,
            'irmao_id'      => $this->irmao_id,
            'deir_comentario' => $this->deir_comentario,
        ];
        return $data;
    }

    public function setDesignacao(Designacao $designacao) {
        $this->designacao = $designacao;
    }

    public function setIrmao(Irmao $irmao) {
        $this->irmao = $irmao;
    }
}
