<?php
// In src/Auth/src/MyAuthAdapterFactory.php:

namespace App;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use \App\Model\UsuarioTable;

class MyAuthAdapterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        // Retrieve any dependencies from the container when creating the instance
        $config = $container->get('config');
        return new MyAuthAdapter(
            $config['remin']['admin'],
            $container->get(UsuarioTable::class)
        );
    }
}
