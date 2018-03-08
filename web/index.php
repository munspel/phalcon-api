<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Mvc\Model\Manager as ModelsManager;
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

$di = new \Phalcon\DI\FactoryDefault();

$di->set(
        'db', function () use ($db_config) {
            return new PdoMysql($db_config);
        } 
);
$di->set(
    "modelsManager", function() {
        return new ModelsManager();
    }
);

$app = new Micro($di);
// Controllers
$app->get('/v1/workers', function () {
    $countries = Workers::find();
    
    $response = new Response();
    $response->setHeader('Access-Control-Allow-Origin', '*');
    $response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
    $response->setStatusCode(200, 'Found');
    $response->setJsonContent(
            [
                'status' => 'OK',
                'data' => $countries,
            ]
    );
    return $response;
});
$app->get('/v1/workers/statuses', function () use ($app) {
    $res = $app->modelsManager->executeQuery("SELECT type, status, worker, count(id) as count, max(last_update) as updated from Models\Jobs  group by type, status, worker");
    
    $response = new Response();
    $response->setHeader('Access-Control-Allow-Origin', '*');
    $response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
    $response->setStatusCode(200, 'Found');
    $response->setJsonContent(
            [
                'status' => 'OK',
                'data' => $res,
            ]
    );
    return $response;
});

$app->handle();
