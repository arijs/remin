<?php
// In src/Auth/src/MyAuthAdapter.php:

namespace App;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use \App\Model\Usuario;
use \App\Model\UsuarioTable;
use \App\Model\UsuarioAcesso;
use \App\Model\UsuarioAcessoTable;

class MyAuthAdapter implements AdapterInterface
{
    private $password;
    private $username;
    private $admin;
    private $usuarioTable;
    private $usuarioAcessoTable;

    public function __construct(
        $admin,
        UsuarioTable $usuarioTable,
        UsuarioAcessoTable $usuarioAcessoTable
    ) {
        // Likely assign dependencies to properties
        $this->admin = $admin;
        $this->usuarioTable = $usuarioTable;
        $this->usuarioAcessoTable = $usuarioAcessoTable;
    }

    public function setPassword(string $password) : void
    {
        $this->password = $password;
    }

    public function setUsername(string $username) : void
    {
        $this->username = $username;
    }

    /**
     * Performs an authentication attempt
     *
     * @return Result
     */
    public function authenticate()
    {
        // Retrieve the user's information (e.g. from a database)
        // and store the result in $row (e.g. associative array).
        // If you do something like this, always store the passwords using the
        // PHP password_hash() function!

        $u = $this->username;
        $p = $this->password;
        $au = $this->admin['user'];
        $ap = $this->admin['pass'];

        if ($u === $au && $p === $ap) {
            return new Result(Result::SUCCESS, [
                'username' => $u,
                'usuario' => null,
                'acesso' => null,
                'admin' => true,
            ]);
        }

        $usuario = $this->usuarioTable->getUsuarioByEmail($u);
        if (!empty($usuario)) {
            $up = $usuario->usuario_senha;
            $uaut = $usuario->usuario_autorizado;
            if ($up === $p) {
                if (empty($uaut)) {
                    return new Result(Result::FAILURE, $u, [
                        'Sua conta ainda não foi autorizada pelo administrador!',
                    ]);
                }
                $acesso = $this->usuarioAcessoTable->getAcessoDeHoje($usuario->usuario_id);
                if (empty($acesso)) {
                    $acesso = $this->usuarioAcessoTable->criarAcessoDeHoje($usuario->usuario_id);
                } else {
                    $acesso = $this->usuarioAcessoTable->updateAcessoDeHoje($acesso);
                }
                return new Result(Result::SUCCESS, [
                    'username' => $u,
                    'usuario' => $usuario,
                    'acesso' => $acesso,
                    'admin' => false
                ]);
            }
        }

        return new Result(Result::FAILURE_CREDENTIAL_INVALID, $u);
    }
}
