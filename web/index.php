<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Http\Response;
use Models\Workers;

//App init
$db_config = require_once(__DIR__ . '/../src/config/db.php');
$loader = new Loader();
$loader->registerNamespaces(
        [
            'Models' => __DIR__ . '/../src/models/',
        ]
);
$loader->register();
$di = new FactoryDefault();
$di->set(
        'db', function () use ($db_config) {
    return new PdoMysql($db_config);
}
);
$app = new Micro($di);

// Controllers
$app->get('/v1/workers', function () {
    $countries = Workers::find();
    $response = new Response();
    $response->setStatusCode(200, 'Found');
    $response->setJsonContent(
            [
                'status' => 'OK',
                'data' => $countries,
            ]
    );
    return $response;
});

$app->handle();
