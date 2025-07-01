<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class Auth extends ServicioCURL
{
    protected $container;
    private const ENDPOINT = "/auth";

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    // Iniciar sesión (genera tokens)
    public function iniciar(Request $request, Response $response, $args)
    {
        $body = $request->getBody()->getContents();
        $respA = $this->ejecutarCURL(self::ENDPOINT , 'PATCH', $body);
        $response->getBody()->write($respA['resp']);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
    }

    // Refrescar token
    public function refrescar(Request $request, Response $response, $args)
    {
        $body = $request->getBody()->getContents();
        $respA = $this->ejecutarCURL(self::ENDPOINT . "/refrescar", 'PATCH', $body);
        $response->getBody()->write($respA['resp']);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
    }

    // Cerrar sesión
    public function cerrar(Request $request, Response $response, $args)
    {
        $identificacion = $args['identificacion'];
        $respA = $this->ejecutarCURL(self::ENDPOINT . "/cerrar/" . $identificacion, 'DELETE');
        $response->getBody()->write($respA['resp']);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
    }
}