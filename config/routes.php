<?php
/**
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Action\HomePageAction::class, 'home');
 * $app->post('/album', App\Action\AlbumCreateAction::class, 'album.create');
 * $app->put('/album/:id', App\Action\AlbumUpdateAction::class, 'album.put');
 * $app->patch('/album/:id', App\Action\AlbumUpdateAction::class, 'album.patch');
 * $app->delete('/album/:id', App\Action\AlbumDeleteAction::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Action\ContactAction::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Action\ContactAction::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Action\ContactAction::class,
 *     Zend\Expressive\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

/** @var \Zend\Expressive\Application $app */

$app->route('/login', App\Action\LoginAction::class, ['GET', 'POST'], 'login');
$app->get('/logout', App\Action\LogoutAction::class, 'logout');
$app->get('/', App\Action\HomePageAction::class, 'home');
$app->get('/api/ping', App\Action\PingAction::class, 'api.ping');
$app->get('/irmaos', [
	App\Action\AuthAction::class,
	App\Action\IrmaosIndexAction::class,
], 'irmaos.index');
$app->post('/irmaos/inserir', [
	App\Action\AuthAction::class,
	App\Action\IrmaosInserirAction::class
], 'irmaos.inserir');
$app->get('/saidas', [
	App\Action\AuthAction::class,
	App\Action\SaidasIndexAction::class
], 'saidas.index');
$app->post('/saidas/inserir', [
	App\Action\AuthAction::class,
	App\Action\SaidasInserirAction::class
], 'saidas.inserir');
$app->get('/designacoes', [
	App\Action\AuthAction::class,
	App\Action\DesignacoesIndexAction::class
], 'designacoes.index');
$app->post('/designacoes/inserir', [
	App\Action\AuthAction::class,
	App\Action\DesignacoesInserirAction::class
], 'designacoes.inserir');
$app->route('/designacoes/editar/{id:\d+}', [
	App\Action\AuthAction::class,
	App\Action\DesignacoesEditarAction::class
], ['GET', 'POST'], 'designacoes.editar');
