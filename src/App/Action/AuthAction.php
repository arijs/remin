<?php

// In src/Auth/src/Action/AuthAction.php:
namespace App\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Diactoros\Response\RedirectResponse;
use \App\InjectAuthMiddleware;

class AuthAction implements ServerMiddlewareInterface
{
    private $auth;

    public function __construct(AuthenticationService $auth)
    {
        $this->auth = $auth;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $identity = $request->getAttribute(InjectAuthMiddleware::class);

        if (empty($identity)) {
            return new RedirectResponse('/login');
        }

        return $delegate->process($request);
    }
}
