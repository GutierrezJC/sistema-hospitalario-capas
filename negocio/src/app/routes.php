<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Bienvenido al Servidor de Negocios");
    return $response;
});




// Agrupa todas las rutas bajo el prefijo /api
$app->group('/api', function (RouteCollectorProxy $api) {
    

    $api->group('/auth', function (RouteCollectorProxy $endpoint) {
        $endpoint->patch('', Auth::class . ':iniciar');
        $endpoint->patch('/refrescar', Auth::class . ':refrescar');
        $endpoint->delete('/{identificacion}', Auth::class . ':cerrar');
    });
    // Grupo de rutas para el recurso artefacto
    $api->group('/administrador', function (RouteCollectorProxy $endpoint) {
        // Obtener artefactos o uno por ID
         $endpoint->get('/{id}', Administrador::class . ':buscar');
        $endpoint->post('', Administrador::class . ':create');                  // Crear artefacto
        $endpoint->put('/{identificacion}', Administrador::class . ':update');              // Actualizar artefacto
        $endpoint->delete('/{identificacion}', Administrador::class . ':delete');           // Eliminar artefacto
        $endpoint->get('/filtrar/{pag}/{lim}', Administrador::class . ':filtrar'); // Filtrar artefactos
    });

    $api->group('/visita', function (RouteCollectorProxy $endpoint) {
        // Obtener artefactos o uno por ID
        $endpoint->get('/buscar/{id}', Visita::class . ':buscarVisita'); // Buscar visita por identificacion
        $endpoint->get('/filtrar', Visita::class . ':filtrar');
        $endpoint->post('', Visita::class . ':create');                  // Crear artefacto
        $endpoint->put('/{id_visita}', Visita::class . ':update');              // Actualizar artefacto
        $endpoint->delete('/{id_visita}', Visita::class . ':delete');           // Eliminar artefacto
        $endpoint->get('/filtrar/{pag}/{lim}', Visita::class . ':filtrar'); // Filtrar artefactos
    });

    // Grupo de rutas para el recurso cliente
    $api->group('/visitante', function (RouteCollectorProxy $endpoint) {

        // Obtener clientes o uno por ID
        $endpoint->post('', Visitante::class . ':create');
        $endpoint->get('/{id}', Visitante::class . ':buscar');                 // Crear cliente
        $endpoint->put('/{identificacion}', Visitante::class . ':update');                // Actualizar cliente
        $endpoint->delete('/{identificacion}', Visitante::class . ':delete');             // Eliminar cliente
        $endpoint->get('/filtrar/{pag}/{lim}', Visitante::class . ':filtrar'); // Filtrar clientes
    });

    // Grupo de rutas para el recurso administrador
    $api->group('/motivovisita', function (RouteCollectorProxy $endpoint) {
        $endpoint->post('', MotivoVisita::class . ':create');              // Crear administrador
        $endpoint->put('/{id_motivo}', MotivoVisita::class . ':update');          // Actualizar administrador
        $endpoint->delete('/{id_motivo}', MotivoVisita::class . ':delete');       // Eliminar administrador
        $endpoint->get('/filtrar/{pag}/{lim}', MotivoVisita::class . ':filtrar'); // Filtrar administradores
    });

    // prueba de autenticar un usuario 
    $api->group('/user', function (RouteCollectorProxy $endpoint) {
        $endpoint->patch('/autenticar', Usuario::class . ':autenticarEndpoint');
    });

    // Grupo de rutas para operaciones de usuario (contraseña y rol)
    $api->group('/usuario', function (RouteCollectorProxy $endpoint) {
        $endpoint->patch('/reset/{identificacion}', Usuario::class . ':resetPassw');    // Restablecer contraseña
        $endpoint->patch('/change/{identificacion}', Usuario::class . ':changePassw');  // Cambiar contraseña
        $endpoint->patch('/rol/{identificacion}', Usuario::class . ':changeRol');       // Cambiar rol
    });

   
});
