<?php

namespace App\Action;

use \App\MyAuthAdapter;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class LogoutAction implements ServerMiddlewareInterface
{
    private $auth;
    private $authAdapter;
    private $template;

    public function __construct(
        TemplateRendererInterface $template,
        AuthenticationService $auth,
        MyAuthAdapter $authAdapter
    ) {
        $this->template    = $template;
        $this->auth        = $auth;
        $this->authAdapter = $authAdapter;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $this->auth->clearIdentity();

        return new RedirectResponse('/login');
    }

}
