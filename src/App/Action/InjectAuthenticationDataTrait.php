<?php
namespace App\Action;

use Psr\Http\Message\ServerRequestInterface;
use \App\InjectAuthMiddleware;

trait InjectAuthenticationDataTrait
{

    public function getAuth(ServerRequestInterface $request)
    {
        return $request->getAttribute(InjectAuthMiddleware::class);
    }

    public function injectAuth(ServerRequestInterface $request, &$data)
    {
        $data['authentication'] = $request->getAttribute(InjectAuthMiddleware::class);
    }
}
