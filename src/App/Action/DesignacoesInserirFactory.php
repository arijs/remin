<?php

namespace App\Action;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class DesignacoesInserirFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router   = $container->get(RouterInterface::class);
        $template = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $designacaoTable = $container->get(\App\Model\DesignacaoTable::class);
        $designacaoIrmaoTable = $container->get(\App\Model\DesignacaoIrmaoTable::class);
        $designacaoSaidaTable = $container->get(\App\Model\DesignacaoSaidaTable::class);

        return new DesignacoesInserirAction($router, $template, $designacaoTable, $designacaoIrmaoTable, $designacaoSaidaTable);
    }
}
