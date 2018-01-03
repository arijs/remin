<?php

namespace App;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Exception;

class InjectAuthMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new InjectAuthMiddleware($container->get(AuthenticationService::class));
    }
}
