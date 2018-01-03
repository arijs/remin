<?php

namespace App\Model;

class Irmao
{
    public $irmao_id;
    public $irmao_nome;

    public function exchangeArray(array $data)
    {
        $this->irmao_id   = !empty($data['irmao_id']) ? $data['irmao_id'] : null;
        $this->irmao_nome = !empty($data['irmao_nome']) ? $data['irmao_nome'] : null;
    }

    public function toArray() {
      $data = [
          'irmao_id' => $this->irmao_id,
          'irmao_nome'  => $this->irmao_nome,
      ];
      return $data;
    }
}
