# SXF - Config Component

The Config component based on Illuminate and DotEnv packages.


## Features

- store authorization values in .env
- use cached configuration
- default values for ENV
- configuration structure based on filesystem (nested files) 


##### Example usage:

1. Use [Composer](http://getcomposer.org) to install Whoops into your project:

    ```bash
    composer require gulios/sxf-config
    ```

1. Initialize in your code:


```php
$basePath = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR;

$configuration = new Config();
$configuration->setConfigFilesPath($basePath . 'config/');
$configuration->setEnvFile($basePath . '.env');
$configuration->setCacheConfigFile($basePath . 'cache/configuration.php');
```

You can get configuration by:
```
$configuration->getAll();
```
or
```
$configuration->get('app.debug')

```
etc

##### Configuration data
Package scan all .php files in defined path (setConfigFilesPath()).

Example file 'config/app.php'
```php
return [
    'first_test' => env('TEST', 'defaultvalue'),
    'second_test' => [
        'key' => 'value'
    ]
];
```
As you see if you don't set TEST value in .env file it will get 'defaultvalue'.

Config directory structure can be nested.



##### Cache

You can execute just:
```php
$configuration->clearCache();
or
$configuration->createCache();
```
or if you use Symfony console you can use two commands:
```php
$config = new Config();
$config->setConfigFilesPath($basePath . 'config/');
$config->setEnvFile($basePath . '.env');
$config->setCacheConfigFile($basePath . 'cache/configuration.php');

$app = new Application();

$app->add(
    new ConfigCacheCommand(
        $config->getConfigFilesPath(),
        $config->getEnvFile(),
        $config->getCacheConfigFile()
    )
);
$app->add(
    new ConfigCacheClearCommand(
        $config->getConfigFilesPath(),
        $config->getEnvFile(),
        $config->getCacheConfigFile()
    )
);

$app->run();

```
and run:
```bash
php bin/console config:
  Command "config:" is ambiguous.
  Did you mean one of these?
      config:create Create a cache file for faster configuration loading
      config:clear  Remove the configuration cache file

```
it will create cache file with all your configuration. 

