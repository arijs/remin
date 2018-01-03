<?php
// In src/Auth/src/MyAuthAdapter.php:

namespace App;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class MyAuthAdapter implements AdapterInterface
{
    private $password;
    private $username;
    private $admin;

    public function __construct($admin)
    {
        // Likely assign dependencies to properties
        $this->admin = $admin;
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
            return new Result(Result::SUCCESS, ['username' => $u]);
        }

        return new Result(Result::FAILURE_CREDENTIAL_INVALID, $this->username);
    }
}
