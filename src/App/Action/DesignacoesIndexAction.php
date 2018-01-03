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

class DesignacoesIndexAction implements ServerMiddlewareInterface
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
        $query  = $request->getQueryParams();
        $count = $this->designacaoTable->countAll();
        $rowsPage = 40;
        $lastPage = ceil($count['count'] / $rowsPage);
        $page = isset($query['page']) ? intval($query['page']) : $lastPage;
        $page = $page < 1 ? 1 : $page;
        $offset = ($page - 1) * $rowsPage;
        $irmaos = $this->irmaoTable->fetchAllArray();
        $irmaosMap = $this->irmaoTable->listToMap($irmaos);
        $saidas = $this->saidaTable->fetchAllArray();
        $saidasMap = $this->saidaTable->listToMap($saidas);
        $list = $this->designacaoTable->fetchOffsetLimitArray($offset, $rowsPage);
        foreach ($list as $desig) {
            $this->designacaoIrmaoTable->fetchDesignacaoIrmaos($desig, $irmaosMap);
            $this->designacaoSaidaTable->fetchDesignacaoSaidas($desig, $saidasMap);
        }
        if (! $this->template) {
            return new JsonResponse([
                'numero_territorios' => $this->numeroTerritorios,
                'result' => $count,
                'list' => $list->toArray(),
                'irmaos' => $irmaos->toArray(),
                'saidas' => $saidas->toArray(),
            ]);
        }

        $data = [];
        $data['numero_territorios'] = $this->numeroTerritorios;
        $data['result'] = $count;
        $data['list'] = $list;
        $data['irmaos'] = $irmaos;
        $data['saidas'] = $saidas;

        $this->injectAuth($request, $data);

        return new HtmlResponse($this->template->render('app::designacoes-index', $data));
    }
}
