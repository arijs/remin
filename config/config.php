<?php

use Zend\ConfigAggregator\ArrayProvider;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

class ConfigFactory
{
    public function createConfig($instanceName)
    {
        // To enable or disable caching, set the `ConfigAggregator::ENABLE_CACHE` boolean in
        // `config/autoload/local.php`.
        $cacheConfig = [
            'config_cache_path' => 'data/config-cache-'.$instanceName.'.php',
        ];

        $aggregator = new ConfigAggregator([
            \Zend\Session\ConfigProvider::class,
            \Zend\Db\ConfigProvider::class,
            // \Zend\Validator\ConfigProvider::class,
            // Include cache configuration
            new ArrayProvider($cacheConfig),

            // Default App module config
            App\ConfigProvider::class,

            // Load application config in a pre-defined order in such a way that local settings
            // overwrite global settings. (Loaded as first to last):
            //   - `global.php`
            //   - `*.global.php`
            //   - `local.php`
            //   - `*.local.php`
            new PhpFileProvider(realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php'),
            new PhpFileProvider(realpath(__DIR__) . '/instances/'.$instanceName.'/{{,*.}global,{,*.}local}.php'),

            // Load development config if it exists
            new PhpFileProvider(realpath(__DIR__) . '/development.config.php'),
        ], $cacheConfig['config_cache_path']);

        return $aggregator->getMergedConfig();

    }
}

return new ConfigFactory();
