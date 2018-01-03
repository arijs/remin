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
use \App\Model\Irmao;

class IrmaosInserirAction implements ServerMiddlewareInterface
{
    use InjectAuthenticationDataTrait;

    private $router;
    private $template;
    private $irmaoTable;

    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template = null,
        \App\Model\IrmaoTable $irmaoTable
    )
    {
        $this->router   = $router;
        $this->template = $template;
        $this->irmaoTable = $irmaoTable;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $post = $request->getParsedBody();
        $irmao = new Irmao();
        $irmao->irmao_nome = $post['nome'];
        $this->irmaoTable->insertIrmao($irmao);
        if (! $this->template) {
            return new JsonResponse([
                'post' => $post,
                'irmao' => $irmao->toArray(),
            ]);
        }

        $data = [];
        $data['post'] = $post;
        $data['irmao'] = $irmao;

        $this->injectAuth($request, $data);

        return new HtmlResponse($this->template->render('app::irmaos-inserir', $data));
    }
}
