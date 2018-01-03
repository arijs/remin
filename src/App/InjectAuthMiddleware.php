<?php

namespace App;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Authentication\AuthenticationService;

class InjectAuthMiddleware implements ServerMiddlewareInterface
{
    private $auth;

    public function __construct(AuthenticationService $auth)
    {
        $this->auth = $auth;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $identity = null;
        if ($this->auth->hasIdentity()) {
            $identity = $this->auth->getIdentity();
        }

        return $delegate->process($request
            ->withAttribute(self::class, $identity)
            ->withAttribute('auth', $identity)
        );
    }
}
