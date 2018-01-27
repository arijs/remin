<?php

namespace App;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Authentication\AuthenticationService;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'invokables' => [
                Action\PingAction::class => Action\PingAction::class,
            ],
            'factories' => [
                InjectAuthMiddleware::class => InjectAuthMiddlewareFactory::class,
                Action\AuthAction::class => Action\AuthActionFactory::class,
                Action\LoginAction::class => Action\LoginActionFactory::class,
                Action\LogoutAction::class => Action\LogoutActionFactory::class,
                Action\UsuarioCadastrarAction::class => Action\UsuarioCadastrarFactory::class,
                Action\HomePageAction::class => Action\HomePageFactory::class,
                Action\IrmaosIndexAction::class => Action\IrmaosIndexFactory::class,
                Action\IrmaosInserirAction::class => Action\IrmaosInserirFactory::class,
                Action\IrmaosDesignacoesAction::class => Action\IrmaosDesignacoesFactory::class,
                Action\SaidasIndexAction::class => Action\SaidasIndexFactory::class,
                Action\SaidasInserirAction::class => Action\SaidasInserirFactory::class,
                Action\DesignacoesIndexAction::class => Action\DesignacoesIndexFactory::class,
                Action\DesignacoesInserirAction::class => Action\DesignacoesInserirFactory::class,
                Action\DesignacoesEditarAction::class => Action\DesignacoesEditarFactory::class,
                Action\DesignacoesRemoverAction::class => Action\DesignacoesRemoverFactory::class,
                /* */
                AuthenticationService::class => AuthenticationServiceFactory::class,
                MyAuthAdapter::class => MyAuthAdapterFactory::class,
                /* */
                Model\IrmaoTable::class => function($container) {
                    $tableGateway = $container->get(Model\IrmaoTableGateway::class);
                    return new Model\IrmaoTable($tableGateway);
                },
                Model\IrmaoTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Irmao());
                    return new TableGateway('irmaos', $dbAdapter, null, $resultSetPrototype);
                },
                /* */
                Model\SaidaTable::class => function($container) {
                    $tableGateway = $container->get(Model\SaidaTableGateway::class);
                    return new Model\SaidaTable($tableGateway);
                },
                Model\SaidaTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Saida());
                    return new TableGateway('saidas', $dbAdapter, null, $resultSetPrototype);
                },
                /* */
                Model\DesignacaoTable::class => function($container) {
                    $tableGateway = $container->get(Model\DesignacaoTableGateway::class);
                    return new Model\DesignacaoTable($tableGateway);
                },
                Model\DesignacaoTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Designacao());
                    return new TableGateway('designacoes', $dbAdapter, null, $resultSetPrototype);
                },
                /* */
                Model\DesignacaoIrmaoTable::class => function($container) {
                    $tableGateway = $container->get(Model\DesignacaoIrmaoTableGateway::class);
                    return new Model\DesignacaoIrmaoTable($tableGateway);
                },
                Model\DesignacaoIrmaoTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\DesignacaoIrmao());
                    return new TableGateway('designacoes_irmaos', $dbAdapter, null, $resultSetPrototype);
                },
                /* */
                Model\DesignacaoSaidaTable::class => function($container) {
                    $tableGateway = $container->get(Model\DesignacaoSaidaTableGateway::class);
                    return new Model\DesignacaoSaidaTable($tableGateway);
                },
                Model\DesignacaoSaidaTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\DesignacaoSaida());
                    return new TableGateway('designacoes_saidas', $dbAdapter, null, $resultSetPrototype);
                },
                /* */
                Model\UsuarioTable::class => function($container) {
                    $tableGateway = $container->get(Model\UsuarioTableGateway::class);
                    return new Model\UsuarioTable($tableGateway);
                },
                Model\UsuarioTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Usuario());
                    return new TableGateway('usuarios', $dbAdapter, null, $resultSetPrototype);
                },
                /* */
                Model\UsuarioAcessoTable::class => function($container) {
                    $tableGateway = $container->get(Model\UsuarioAcessoTableGateway::class);
                    return new Model\UsuarioAcessoTable($tableGateway);
                },
                Model\UsuarioAcessoTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\UsuarioAcesso());
                    return new TableGateway('usuarios_acessos', $dbAdapter, null, $resultSetPrototype);
                },
                /* */
            ],
        ];
    }

    /**
     * Returns the templates configuration
     *
     * @return array
     */
    public function getTemplates()
    {
        return [
            'paths' => [
                'app'    => ['templates/app'],
                'forms'  => ['templates/forms'],
                'error'  => ['templates/error'],
                'layout' => ['templates/layout'],
            ],
        ];
    }
}
