<?php

// In src/Auth/src/Action/LoginActionFactory.php:

namespace App\Action;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Expressive\Template\TemplateRendererInterface;
use \App\MyAuthAdapter;
use \App\Model\UsuarioTable;

class LoginActionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new LoginAction(
            $container->get(TemplateRendererInterface::class),
            $container->get(AuthenticationService::class),
            $container->get(MyAuthAdapter::class),
            $container->get(UsuarioTable::class)
        );
    }
}
