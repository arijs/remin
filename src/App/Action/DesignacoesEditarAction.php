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
use \App\Model\Designacao;
use \App\Model\DesignacaoIrmao;
use \App\Model\DesignacaoSaida;

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
        if ($request->getMethod() === 'POST') {
            return $this->processPost($request);
        } else {
            return $this->processGet($request);
        }
    }

    public function processGet(ServerRequestInterface $request)
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
                'designacao' => $desig->toArray(),
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

    public function processPost(ServerRequestInterface $request)
    {
        $id = (int) $request->getAttribute('id');
        $post = $request->getParsedBody();
        $designacao = new Designacao();
        $designacao->exchangeArray([
            'designacao_id' => $id,
            'designacao_territorio' => $post['territorio'],
            'designacao_entrega' => $post['data_entrega'],
            'designacao_devolucao' => $post['data_devolucao'],
            'designacao_comentario' => $post['comentario'],
        ]);
        $this->designacaoTable->updateDesignacao($designacao);
        $this->designacaoIrmaoTable->deleteDesignacao($id);
        $this->designacaoSaidaTable->deleteDesignacao($id);
        if (!empty($post['irmaos_id'])) {
            $this->designacaoIrmaoTable->insertFromArrays(
                $designacao,
                $post['irmaos_id'],
                !empty($post['irmaos_comentario']) ? $post['irmaos_comentario'] : []
            );
        }
        // $this->designacaoIrmaoTable->insertFromArrays($designacao, $post['irmaos_id'], $post['irmaos_comentario']);
        if (!empty($post['saidas_id'])) {
            $this->designacaoSaidaTable->insertFromArrays(
                $designacao,
                $post['saidas_id'],
                !empty($post['saidas_comentario']) ? $post['saidas_comentario'] : []
            );
        }
        // $this->designacaoSaidaTable->insertFromArrays($designacao, $post['saidas_id'], $post['saidas_comentario']);

        if (! $this->template) {
            return new JsonResponse([
                'post' => $post,
                'designacao' => $designacao->toArray(),
                'designacao_irmaos' => $designacao->getDesignacaoIrmaosArray(),
                'designacao_saidas' => $designacao->getDesignacaoSaidasArray(),
            ]);
        }

        $data = [];
        $data['editar'] = $id;
        $data['post'] = $post;
        $data['designacao'] = $designacao;

        $this->injectAuth($request, $data);

        return new HtmlResponse($this->template->render('app::designacoes-inserir', $data));
    }
}
