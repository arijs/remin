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

class IrmaosIndexAction implements ServerMiddlewareInterface
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
        $query  = $request->getQueryParams();
        $page = isset($query['page']) ? intval($query['page']) : 1;
        $page = $page < 1 ? 1 : $page;
        $rowsPage = 50;
        $offset = ($page - 1) * $rowsPage;
        $count = $this->irmaoTable->countAll();
        $list = $this->irmaoTable->fetchOffsetLimit($offset, $rowsPage);
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

        return new HtmlResponse($this->template->render('app::irmaos-index', $data));
    }
}
