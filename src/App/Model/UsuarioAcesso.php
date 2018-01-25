<?php

namespace App\Model;

class UsuarioAcesso
{
    public $usuario_id;
    public $usuario_data;
    public $usuario_contagem;
    public $usuario_hora_inicial;
    public $usuario_hora_final;

    public function exchangeArray(array $data)
    {
        $this->usuario_id = !empty($data['usuario_id']) ? $data['usuario_id'] : null;
        $this->usuario_data  = !empty($data['usuario_data']) ? $data['usuario_data'] : null;
        $this->usuario_contagem = !empty($data['usuario_contagem']) ? $data['usuario_contagem'] : null;
        $this->usuario_hora_inicial = !empty($data['usuario_hora_inicial']) ? $data['usuario_hora_inicial'] : null;
        $this->usuario_hora_final = !empty($data['usuario_hora_final']) ? $data['usuario_hora_final'] : null;
    }

    public function toArray() {
      $data = [
          'usuario_id' => $this->usuario_id,
          'usuario_data'  => $this->usuario_data,
          'usuario_contagem' => $this->usuario_contagem,
          'usuario_hora_inicial' => $this->usuario_hora_inicial,
          'usuario_hora_final' => $this->usuario_hora_final,
      ];
      return $data;
    }
}
