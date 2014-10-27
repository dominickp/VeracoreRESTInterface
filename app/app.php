<?php

require_once __DIR__.'/../app/bootstrap.php';

use Shawmut\VeracoreApi\VeracoreOrder;

$VeracoreOrder = new VeracoreOrder();

$app['debug'] = true;

$app = new Silex\Application();

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
});

return $app;