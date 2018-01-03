<?php

use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

class ContainerFactory
{
	public function createContainer($instanceName)
	{
		// Load configuration
		$configFactory = require __DIR__ . '/config.php';

		$config = $configFactory->createConfig($instanceName);

		// Build container
		$container = new ServiceManager();
		(new Config($config['dependencies']))->configureServiceManager($container);

		// Inject config
		$container->setService('config', $config);

		return $container;
	}
}

return new ContainerFactory();
