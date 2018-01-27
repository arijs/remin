<?php
// In src/Auth/src/Action/LoginAction.php:

namespace App\Action;

use \App\MyAuthAdapter;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use \App\Model\Usuario;
use \App\Model\UsuarioTable;

class LoginAction implements ServerMiddlewareInterface
{
    private $template;
    private $auth;
    private $authAdapter;
    private $usuarioTable;

    public function __construct(
        TemplateRendererInterface $template,
        AuthenticationService $auth,
        MyAuthAdapter $authAdapter,
        UsuarioTable $usuarioTable
    ) {
        $this->template     = $template;
        $this->auth         = $auth;
        $this->authAdapter  = $authAdapter;
        $this->usuarioTable = $usuarioTable;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if ($request->getMethod() === 'POST') {
            return $this->authenticate($request);
        }

        return new HtmlResponse($this->template->render('app::login'));
    }

    public function authenticate(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();

        if (empty($params['username'])) {
            return new HtmlResponse($this->template->render('app::login', [
                'error' => 'The username cannot be empty',
            ]));
        }

        if (empty($params['password'])) {
            return new HtmlResponse($this->template->render('app::login', [
                'username' => $params['username'],
                'error'    => 'The password cannot be empty',
            ]));
        }

        $this->authAdapter->setUsername($params['username']);
        $this->authAdapter->setPassword($params['password']);

        $result = $this->auth->authenticate();
        if (!$result->isValid()) {
            $msgs = $result->getMessages();
            if (empty($msgs)) {
                $msgs = 'Usuario ou senha inválidos';
            } else {
                $msgs = implode(' / ', $msgs);
            }
            return new HtmlResponse($this->template->render('app::login', [
                'username' => $params['username'],
                'error'    => $msgs,
            ]));
        }

        return new RedirectResponse('/');
    }
}
