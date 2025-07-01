<?php

 namespace App\controllers;

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Container\ContainerInterface;

    use PDO;


class Visitante extends Persona
{
    private const ROL = 2; // Usa el rol que corresponda a visitante en tu sistema
    private const RECURSO = "Visitante";

  

    public function create(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody(), 1);
        $status = $this->createP(self::RECURSO, self::ROL, $body);

        return $response->withStatus($status);
    }

    public function update(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody(), 1);
        $status = $this->updateP(self::RECURSO, $body, $args['identificacion']);
        return $response->withStatus($status);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $status = $this->deleteP(self::RECURSO, $args['identificacion']);
        return $response->withStatus($status);
    }

    public function filtrar(Request $request, Response $response, $args)
    {
        $datos = $request->getQueryParams();
        $resp = $this->filtrarP(self::RECURSO, $datos, $args['pag'], $args['lim']);
        $response->getbody()->write(json_encode($resp['datos']));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($resp['status']);
    }

    public function buscar(Request $request, Response $response, $args){
  
        $resp = $this->buscarP(self::RECURSO,   $args['id']);
        $response->getbody()->write(json_encode($resp['datos']));


        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($resp['status']);

    }
}