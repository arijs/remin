<?php

namespace App\Action;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class UsuarioCadastrarFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router   = $container->get(RouterInterface::class);
        $template = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $usuarioTable = $container->get(\App\Model\UsuarioTable::class);
        // $config = $container->get('config');

        return new UsuarioCadastrarAction(
            $router,
            $template,
            $usuarioTable
            // $config['remin']['numero_territorios']
        );
    }
}
