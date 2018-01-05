<?php

namespace App\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Expressive\Plates\PlatesRenderer;
use Zend\Expressive\Twig\TwigRenderer;
use Zend\Expressive\ZendView\ZendViewRenderer;

class DesignacoesEditarAction implements ServerMiddlewareInterface
{
    use InjectAuthenticationDataTrait;

    private $router;
    private $template;
    private $designacaoTable;
    private $irmaoTable;
    private $saidaTable;
    private $designacaoIrmaoTable;
    private $designacaoSaidaTable;
    private $numeroTerritorios;

    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template = null,
        \App\Model\DesignacaoTable $designacaoTable,
        \App\Model\IrmaoTable $irmaoTable,
        \App\Model\SaidaTable $saidaTable,
        \App\Model\DesignacaoIrmaoTable $designacaoIrmaoTable,
        \App\Model\DesignacaoSaidaTable $designacaoSaidaTable,
        $numeroTerritorios = null
    )
    {
        $this->router   = $router;
        $this->template = $template;
        $this->designacaoTable = $designacaoTable;
        $this->irmaoTable = $irmaoTable;
        $this->saidaTable = $saidaTable;
        $this->designacaoIrmaoTable = $designacaoIrmaoTable;
        $this->designacaoSaidaTable = $designacaoSaidaTable;
        $this->numeroTerritorios = $numeroTerritorios;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');
        $desig = $this->designacaoTable->getDesignacao($id);
        $irmaos = $this->irmaoTable->fetchAllArray();
        $irmaosMap = $this->irmaoTable->listToMap($irmaos);
        $saidas = $this->saidaTable->fetchAllArray();
        $saidasMap = $this->saidaTable->listToMap($saidas);
        $this->designacaoIrmaoTable->fetchDesignacaoIrmaos($desig, $irmaosMap);
        $this->designacaoSaidaTable->fetchDesignacaoSaidas($desig, $saidasMap);
        if (! $this->template) {
            return new JsonResponse([
                'numero_territorios' => $this->numeroTerritorios,
                'designacao' => $desig,
                'irmaos' => $irmaos,
                'saidas' => $saidas,
            ]);
        }

        $data = [];
        $data['numero_territorios'] = $this->numeroTerritorios;
        $data['designacao'] = $desig;
        $data['irmaos'] = $irmaos;
        $data['saidas'] = $saidas;

        $this->injectAuth($request, $data);

        return new HtmlResponse($this->template->render('app::designacoes-editar', $data));
    }
}
