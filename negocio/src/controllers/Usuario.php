<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class Usuario extends ServicioCURL
{
    protected $container;
    private const ENDPOINT = "/usuario";

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    // Cambiar contraseña
    public function changePassw(Request $request, Response $response, $args)
    {
        $body = $request->getBody()->getContents();
        $url = self::ENDPOINT . "/changePassw/" . $args['identificacion'];
        
        $authHeader = $request->getHeaderLine('Authorization');
        $headers = [];
        if ($authHeader) {
            $headers[] = "Authorization: $authHeader";
        }
        
        $respA = $this->ejecutarCURL($url, 'PATCH', $body, $headers);
        $response->getBody()->write($respA['resp']);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
    }

    // Resetear contraseña
    public function resetPassw(Request $request, Response $response, $args)
    {
        $url = self::ENDPOINT . "/reset/" . $args['identificacion'];
        
        $authHeader = $request->getHeaderLine('Authorization');
        $headers = [];
        if ($authHeader) {
            $headers[] = "Authorization: $authHeader";
        }
        
        $respA = $this->ejecutarCURL($url, 'PATCH', null, $headers);
        $response->getBody()->write($respA['resp']);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
    }

    // Cambiar rol
    public function changeRol(Request $request, Response $response, $args)
    {
        $body = $request->getBody()->getContents();
        $url = self::ENDPOINT . "/rol/" . $args['identificacion'];
        
        $authHeader = $request->getHeaderLine('Authorization');
        $headers = [];
        if ($authHeader) {
            $headers[] = "Authorization: $authHeader";
        }
        
        $respA = $this->ejecutarCURL($url, 'PATCH', $body, $headers);
        $response->getBody()->write($respA['resp']);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
    }
}