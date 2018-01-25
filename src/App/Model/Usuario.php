<?php

namespace App\Model;

class Usuario
{
    public $usuario_id;
    public $irmao_id;
    public $usuario_nome;
    public $usuario_email;
    public $usuario_senha;
    public $usuario_registro;
    public $usuario_autorizado;

    public function exchangeArray(array $data)
    {
        $this->usuario_id = !empty($data['usuario_id']) ? $data['usuario_id'] : null;
        $this->irmao_id   = !empty($data['irmao_id']) ? $data['irmao_id'] : null;
        $this->usuario_nome  = !empty($data['usuario_nome']) ? $data['usuario_nome'] : null;
        $this->usuario_email = !empty($data['usuario_email']) ? $data['usuario_email'] : null;
        $this->usuario_senha = !empty($data['usuario_senha']) ? $data['usuario_senha'] : null;
        $this->usuario_registro = !empty($data['usuario_registro']) ? $data['usuario_registro'] : null;
        $this->usuario_autorizado = !empty($data['usuario_autorizado']) ? $data['usuario_autorizado'] : null;
    }

    public function toArray() {
      $data = [
          'usuario_id' => $this->usuario_id,
          'irmao_id' => $this->irmao_id,
          'usuario_nome'  => $this->usuario_nome,
          'usuario_email' => $this->usuario_email,
          'usuario_senha' => $this->usuario_senha,
          'usuario_registro' => $this->usuario_registro,
          'usuario_autorizado' => $this->usuario_autorizado,
      ];
      return $data;
    }
}
