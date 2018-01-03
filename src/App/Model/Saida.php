<?php

namespace App\Model;

class Saida
{
    public $saida_id;
    public $saida_nome;

    public function exchangeArray(array $data)
    {
        $this->saida_id   = !empty($data['saida_id']) ? $data['saida_id'] : null;
        $this->saida_nome = !empty($data['saida_nome']) ? $data['saida_nome'] : null;
    }

    public function toArray() {
      $data = [
          'saida_id' => $this->saida_id,
          'saida_nome'  => $this->saida_nome,
      ];
      return $data;
    }
}
