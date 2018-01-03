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

class SaidasIndexAction implements ServerMiddlewareInterface
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
        $query  = $request->getQueryParams();
        $page = isset($query['page']) ? intval($query['page']) : 1;
        $page = $page < 1 ? 1 : $page;
        $rowsPage = 20;
        $offset = ($page - 1) * $rowsPage;
        $count = $this->saidaTable->countAll();
        $list = $this->saidaTable->fetchOffsetLimit($offset, $rowsPage);
        if (! $this->template) {
            return new JsonResponse([
                'result' => $count,
                'list' => $list,
            ]);
        }

        $data = [];
        $data['result'] = $count;
        $data['list'] = $list;

        $this->injectAuth($request, $data);

        return new HtmlResponse($this->template->render('app::saidas-index', $data));
    }
}
