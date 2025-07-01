<?php

use Slim\Factory\AppFactory;

use DI\Container;

require __DIR__ . '/../../vendor/autoload.php';

$container = new Container();

AppFactory::SetContainer($container);

$app = AppFactory::create();



// ---  para CORS ---
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    
    // Permitir puertod de origen
    $origin = $request->getHeaderLine('Origin');
    $allowedOrigins = [
        'http://localhost:4200', 
        'http://localhost:4201', 
        'http://localhost:51633'  
    ];
    
    if (in_array($origin, $allowedOrigins)) {
        $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
    }
    
    return $response
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
// ----------------------------

$app->add(function ($request, $handler) {
    try {
        $response = $handler->handle($request);
    } catch (\Throwable $e) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]));
        $response = $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json');
    }
    
    // Permitir múltiples orígenes en error 
    $origin = $request->getHeaderLine('Origin');
    $allowedOrigins = [
        'http://localhost:4200', 
        'http://localhost:4201', 
        'http://localhost:51633'  // Puerto actual de desarrollo
    ];
    
    if (in_array($origin, $allowedOrigins)) {
        $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
    }
    
    return $response
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});




require "routes.php";

$app->run();
