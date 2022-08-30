<?php

declare(strict_types=1);

use App\HelloWorldController;
use DI\ContainerBuilder;
use FastRoute\RouteCollector;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7Server\ServerRequestCreator;
use Relay\Relay;
use function DI\create;
use function DI\get;
use function FastRoute\simpleDispatcher;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    HelloWorldController::class => create(HelloWorldController::class)
        ->constructor(get('Response')),
    'Response' => static function() {
        return new Response();
    }
]);

$container = $containerBuilder->build();

$routes = simpleDispatcher(function (RouteCollector $request) {
    $request->get('/', HelloWorldController::class);
});

$middleware[] =  new FastRoute($routes);
$middleware[] = new RequestHandler($container);

$requestHandler = new Relay($middleware);

$psr17Factory = new Psr17Factory();

$creator = new ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);
$response = $requestHandler->handle($creator->fromGlobals());
return (new \Narrowspark\HttpEmitter\SapiEmitter())->emit($response);