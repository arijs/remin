<?php

// In src/Auth/src/Action/LoginActionFactory.php:

namespace App\Action;

use \App\MyAuthAdapter;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Expressive\Template\TemplateRendererInterface;

class LogoutActionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new LogoutAction(
            $container->get(TemplateRendererInterface::class),
            $container->get(AuthenticationService::class),
            $container->get(MyAuthAdapter::class)
        );
    }
}
