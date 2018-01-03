<?php
// In src/Auth/src/Action/AuthActionFactory.php:

namespace App\Action;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Exception;

class AuthActionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AuthAction($container->get(AuthenticationService::class));
    }
}
