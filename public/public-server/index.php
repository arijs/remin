<?php

// Delegate static file requests back to the PHP built-in webserver
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    $path = $_SERVER['SCRIPT_FILENAME'];
    file_put_contents("php://stdout", "req $path");
    return false;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', true);
error_reporting(E_ALL | E_USER_DEPRECATED | E_DEPRECATED | E_STRICT | E_NOTICE);

// echo __DIR__, '<br/>';
// echo dirname(__DIR__, 2).'/zfapps/tervel', '<br/>';
// echo is_dir(dirname(__DIR__, 2).'/zfapps/tervel'), '<br/>';

// chdir(dirname(__DIR__));
chdir(dirname(__DIR__, 2).'/zfapps/tervel');
require 'vendor/autoload.php';

/**
 * Self-called anonymous function that creates its own scope and keep the global namespace clean.
 */
call_user_func(function () {
    /** @var \Psr\Container\ContainerInterface $container */
    $containerFactory = require 'config/container.php';

    $container = $containerFactory->createContainer('tersoc');

    /** @var \Zend\Expressive\Application $app */
    $app = $container->get(\Zend\Expressive\Application::class);

    // Import programmatic/declarative middleware pipeline and routing
    // configuration statements
    require 'config/pipeline.php';
    require 'config/routes.php';

    $app->run();
});
