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
use \App\Model\Irmao;

class DesignacoesInserirAction implements ServerMiddlewareInterface
{
    use InjectAuthenticationDataTrait;

    private $router;
    private $template;
    private $designacaoTable;
    private $designacaoIrmaoTable;
    private $designacaoSaidaTable;

    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template = null,
        \App\Model\DesignacaoTable $designacaoTable,
        \App\Model\DesignacaoIrmaoTable $designacaoIrmaoTable,
        \App\Model\DesignacaoSaidaTable $designacaoSaidaTable
    )
    {
        $this->router   = $router;
        $this->template = $template;
        $this->designacaoTable = $designacaoTable;
        $this->designacaoIrmaoTable = $designacaoIrmaoTable;
        $this->designacaoSaidaTable = $designacaoSaidaTable;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $post = $request->getParsedBody();
        // $irmao = new Irmao();
        // $irmao->irmao_nome = $post['nome'];
        // $this->irmaoTable->insertIrmao($irmao);
        $designacao = new Designacao();
        $designacao->designacao_territorio = $post['territorio'];
        $designacao->designacao_entrega = $post['data_entrega'];
        $designacao->designacao_devolucao = !empty($post['data_devolucao']) ? $post['data_devolucao'] : '0000-00-00';
        $designacao->designacao_comentario = $post['comentario'];
        $this->designacaoTable->insertDesignacao($designacao);

        $designacaoIrmaos = [];
        $indexIrmao = 0;
        $irmaos_id = !empty($post['irmaos_id']) ? $post['irmaos_id'] : [];
        foreach ($irmaos_id as $irmao_id) {
            $designacaoIrmao = new DesignacaoIrmao();
            $designacaoIrmao->designacao_id = $designacao->designacao_id;
            $designacaoIrmao->irmao_id = $irmao_id;
            $designacaoIrmao->deir_comentario = $post['irmaos_comentario'][$indexIrmao];
            $this->designacaoIrmaoTable->insertDesignacaoIrmao($designacaoIrmao);
            $designacaoIrmaos[] = $designacaoIrmao;
            $indexIrmao++;
        }
        $designacao->setDesignacaoIrmaos($designacaoIrmaos);

        $designacaoSaidas = [];
        $indexSaida = 0;
        $saidas_id = !empty($post['saidas_id']) ? $post['saidas_id'] : [];
        foreach ($saidas_id as $saida_id) {
            $designacaoSaida = new DesignacaoSaida();
            $designacaoSaida->designacao_id = $designacao->designacao_id;
            $designacaoSaida->saida_id = $saida_id;
            $designacaoSaida->desa_comentario = $post['saidas_comentario'][$indexSaida];
            $this->designacaoSaidaTable->insertDesignacaoSaida($designacaoSaida);
            $designacaoSaidas[] = $designacaoSaida;
            $indexSaida++;
        }
        $designacao->setDesignacaoSaidas($designacaoSaidas);

        if (! $this->template) {
            return new JsonResponse([
                'post' => $post,
                'designacao' => $designacao->toArray(),
                'designacao_irmaos' => $designacao->getDesignacaoIrmaosArray(),
                'designacao_saidas' => $designacao->getDesignacaoSaidasArray(),
            ]);
        }

        $data = [];
        $data['post'] = $post;
        $data['designacao'] = $designacao;

        $this->injectAuth($request, $data);

        return new HtmlResponse($this->template->render('app::designacoes-inserir', $data));
    }
}
