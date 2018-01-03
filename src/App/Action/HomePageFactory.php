<?php

namespace App\Action;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Authentication\AuthenticationService;

class HomePageFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router   = $container->get(RouterInterface::class);
        $template = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $irmaoTable = $container->get(\App\Model\IrmaoTable::class);
        $auth = $container->get(AuthenticationService::class);

        return new HomePageAction($router, $template, $irmaoTable, $auth);
    }
}
