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
use \App\Model\Saida;

class SaidasInserirAction implements ServerMiddlewareInterface
{
    use InjectAuthenticationDataTrait;

    private $router;
    private $template;
    private $saidaTable;

    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template = null,
        \App\Model\SaidaTable $saidaTable
    )
    {
        $this->router   = $router;
        $this->template = $template;
        $this->saidaTable = $saidaTable;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $post = $request->getParsedBody();
        $saida = new Saida();
        $saida->saida_nome = $post['nome'];
        $this->saidaTable->insertSaida($saida);
        if (! $this->template) {
            return new JsonResponse([
                'post' => $post,
                'saida' => $saida->toArray(),
            ]);
        }

        $data = [];
        $data['post'] = $post;
        $data['saida'] = $saida;

        $this->injectAuth($request, $data);

        return new HtmlResponse($this->template->render('app::saidas-inserir', $data));
    }
}
