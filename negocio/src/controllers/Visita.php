<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class Visita extends ServicioCURL
{
    protected $container;
    private const ENDPOINT = "/visita";

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    // public function create(Request $request, Response $response, $args)
    // {
    //     $body = $request->getBody()->getContents();
    //     $respA = $this->ejecutarCURL(self::ENDPOINT, 'POST', $body);
    //     $response->getBody()->write($respA['resp']);
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
    // }

    // public function update(Request $request, Response $response, $args)
    // {
    //     $body = $request->getBody()->getContents();
    //     $url = self::ENDPOINT . '/' . $args['id_visita'];
    //     $respA = $this->ejecutarCURL($url, 'PUT', $body);
    //     $response->getBody()->write($respA['resp']);
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
    // }

    // public function delete(Request $request, Response $response, $args)
    // {
    //     $url = self::ENDPOINT . '/' . $args['id_visita'];
    //     $respA = $this->ejecutarCURL($url, 'DELETE');
    //     $response->getBody()->write($respA['resp']);
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
    // }

    // public function filtrar(Request $request, Response $response, $args)
    // {
    //     $pag = 0;
    //     $lim = 10;
    //     $url = self::ENDPOINT . '/filtrar';
    //     $query = http_build_query($request->getQueryParams());
    //     if ($query) $url .= '?' . $query;
    //     $respA = $this->ejecutarCURL($url, 'GET');
    //     $response->getBody()->write($respA['resp']);
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
    // }

    //   public function buscarVisita(Request $request, Response $response, $args)
    // {
    //     $url = self::ENDPOINT . '/buscar/' . $args['id'];
    //     $respA = $this->ejecutarCURL($url, 'GET');
    //     $response->getBody()->write($respA['resp']);
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
    // }

public function create(Request $request, Response $response, $args)
{
    $body = $request->getBody()->getContents();
    $authHeader = $request->getHeaderLine('Authorization');
    $headers = [];
    if ($authHeader) {
        $headers[] = "Authorization: $authHeader";
    }
    $respA = $this->ejecutarCURL(self::ENDPOINT, 'POST', $body, $headers);
    $response->getBody()->write($respA['resp']);
    return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
}

public function update(Request $request, Response $response, $args)
{
    $body = $request->getBody()->getContents();
    $url = self::ENDPOINT . '/' . $args['id_visita'];
    $authHeader = $request->getHeaderLine('Authorization');
    $headers = [];
    if ($authHeader) {
        $headers[] = "Authorization: $authHeader";
    }
    $respA = $this->ejecutarCURL($url, 'PUT', $body, $headers);
    $response->getBody()->write($respA['resp']);
    return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
}

public function delete(Request $request, Response $response, $args)
{
    $url = self::ENDPOINT . '/' . $args['id_visita'];
    $authHeader = $request->getHeaderLine('Authorization');
    $headers = [];
    if ($authHeader) {
        $headers[] = "Authorization: $authHeader";
    }
    $respA = $this->ejecutarCURL($url, 'DELETE', null, $headers);
    $response->getBody()->write($respA['resp']);
    return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
}

public function filtrar(Request $request, Response $response, $args)
{
    $pag = 0;
    $lim = 10;
    $url = self::ENDPOINT . '/filtrar';
    $query = http_build_query($request->getQueryParams());
    if ($query) $url .= '?' . $query;
    $authHeader = $request->getHeaderLine('Authorization');
    $headers = [];
    if ($authHeader) {
        $headers[] = "Authorization: $authHeader";
    }
    $respA = $this->ejecutarCURL($url, 'GET', null, $headers);
    $response->getBody()->write($respA['resp']);
    return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
}

public function buscarVisita(Request $request, Response $response, $args)
{
    $url = self::ENDPOINT . '/buscar/' . $args['id'];
    $authHeader = $request->getHeaderLine('Authorization');
    $headers = [];
    if ($authHeader) {
        $headers[] = "Authorization: $authHeader";
    }
    $respA = $this->ejecutarCURL($url, 'GET', null, $headers);
    $response->getBody()->write($respA['resp']);
    return $response->withHeader('Content-Type', 'application/json')->withStatus($respA['status']);
}



}