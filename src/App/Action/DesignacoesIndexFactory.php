<?php

namespace App\Action;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class DesignacoesIndexFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router   = $container->get(RouterInterface::class);
        $template = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $designacaoTable = $container->get(\App\Model\DesignacaoTable::class);
        $irmaoTable = $container->get(\App\Model\IrmaoTable::class);
        $saidaTable = $container->get(\App\Model\SaidaTable::class);
        $designacaoIrmaoTable = $container->get(\App\Model\DesignacaoIrmaoTable::class);
        $designacaoSaidaTable = $container->get(\App\Model\DesignacaoSaidaTable::class);
        $config = $container->get('config');

        return new DesignacoesIndexAction(
            $router,
            $template,
            $designacaoTable,
            $irmaoTable,
            $saidaTable,
            $designacaoIrmaoTable,
            $designacaoSaidaTable,
            $config['tervel']['numero_territorios']
        );
    }
}
