<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

# Logging
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/log/development.log',
));

# YML configuration
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/settings.yml'));
