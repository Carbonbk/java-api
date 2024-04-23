<?php

declare(strict_types=1);

use App\Middleware\ApiKeyMiddleware;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DI\Container;
use DI\ContainerBuilder;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Psr7\Message;

define('APP_ROOT' , dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';

$builder = new ContainerBuilder();

$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')
                     ->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector =$app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$app->addBodyParsingMiddleware();

$error_middleware = $app->addErrorMiddleware(true, true, true);
$error_handler = $error_middleware->getDefaultErrorHandler();
$error_handler->forceContentType('application/json');

$app->add(new ApiKeyMiddleware);

$app->post('/api/sensoren'                   , App\Controllers\Sensoren::class . ':addSensor');
$app->get ('/api/sensoren'                   , App\Controllers\Sensoren::class . ':showAllSensoren');
$app->get ('/api/sensoren/{id:[0-9]+}'       , App\Controllers\Sensoren::class . ':ShowSensorById');

$app->post('/api/metingen'                   , App\Controllers\Metingen::class . ':addMeting');
$app->get ('/api/metingen'                   , App\Controllers\Metingen::class . ':ShowAllMetingen');
$app->get ('/api/metingen/{id:[0-9]+}'       , App\Controllers\Metingen::class . ':ShowMetingById');
$app->get ('/api/metingen/filter'            , App\Controllers\Metingen::class . ':GetFilteredMetingen');

$app->run();