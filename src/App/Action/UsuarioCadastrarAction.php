<?php
// In src/Auth/src/Action/LoginAction.php:

namespace App\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use \App\Model\Usuario;
use \App\Model\UsuarioTable;

class UsuarioCadastrarAction implements ServerMiddlewareInterface
{
    private $router;
    private $template;
    private $usuarioTable;

    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $template,
        UsuarioTable $usuarioTable
    ) {
        $this->router       = $router;
        $this->template     = $template;
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

        $name = isset($params['name']) ? $params['name'] : null;
        $email = isset($params['email']) ? $params['email'] : null;
        $password = isset($params['password']) ? $params['password'] : null;
        $password_confirm = isset($params['password_confirm']) ? $params['password_confirm'] : null;

        $error = null;

        if (empty($name)) {
            // return new HtmlResponse($this->template->render('app::login', [
            //     'error' => 'The username cannot be empty',
            // ]));
            $error = 'O nome está vazio!';
        }

        else if (empty($email)) {
            $error = 'O e-mail está vazio!';
        }

        else if (empty($password)) {
            // return new HtmlResponse($this->template->render('app::login', [
            //     'username' => $params['username'],
            //     'error'    => 'The password cannot be empty',
            // ]));
            $error = 'A senha está vazia!';
        }

        else if (empty($password_confirm)) {
            $error = 'É necessário confirmar a senha!';
        }

        else if ($password_confirm !== $password) {
            $error = 'A senha e a confirmação da senha são diferentes!';
        }

        else {
            $error = 'Tudo OK! :D';
        }

        // $result = $this->auth->authenticate();
        // if (!$result->isValid()) {
        return new HtmlResponse($this->template->render('app::login', [
            'tabRegister' => true,
            'register_name' => $name,
            'register_email' => $email,
            'register_password' => $password,
            // 'register_' => $,
            'register_error' => $error,
        ]));
        // }

        return new RedirectResponse('/');
    }
}
