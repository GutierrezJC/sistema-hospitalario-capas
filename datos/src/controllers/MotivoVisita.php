<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;

class MotivoVisita
{
    protected $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function create(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody(), true);
        $descripcion = filter_var($body['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        $sql = "SELECT nuevoMotivoVisita(:descripcion) AS resultado;";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        $query->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);

        try {
            $query->execute();
            $res = $query->fetch(PDO::FETCH_ASSOC)['resultado'];
            $status = match($res) {
                0 => 201, // Creado
                1 => 409, // Ya existe
                default => 500
            };
        } catch (\PDOException $e) {
            $status = 500;
        }

        $query = null;
        $con = null;
        return $response->withStatus($status);
    }

    public function update(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody(), true);
        $id_motivo = filter_var($args['id_motivo'], FILTER_SANITIZE_NUMBER_INT);
        $descripcion = filter_var($body['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        $sql = "SELECT editarMotivoVisita(:id_motivo, :descripcion) AS resultado;";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        $query->bindValue(':id_motivo', $id_motivo, PDO::PARAM_INT);
        $query->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);

        try {
            $query->execute();
            $res = $query->fetch(PDO::FETCH_ASSOC)['resultado'];
            $status = match($res) {
                0 => 200, // Editado
                1 => 404, // No encontrado
                default => 500
            };
        } catch (\PDOException $e) {
            $status = 500;
        }

        $query = null;
        $con = null;
        return $response->withStatus($status);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $id_motivo = filter_var($args['id_motivo'], FILTER_SANITIZE_NUMBER_INT);

        $sql = "SELECT eliminarMotivoVisita(:id_motivo) AS resultado;";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        $query->bindValue(':id_motivo', $id_motivo, PDO::PARAM_INT);

        try {
            $query->execute();
            $res = $query->fetch(PDO::FETCH_ASSOC)['resultado'];
            $status = $res > 0 ? 200 : 404;
        } catch (\PDOException $e) {
            $status = 500;
        }

        $query = null;
        $con = null;
        return $response->withStatus($status);
    }

    public function filtrar(Request $request, Response $response, $args)
    {
        $datos = $request->getQueryParams();
        $filtro = filter_var($datos['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $pag = isset($args['pag']) ? (int)$args['pag'] : 0;
        $lim = isset($args['lim']) ? (int)$args['lim'] : 10;

        $sql = "CALL filtrarMotivoVisita(:filtro, :pag, :lim);";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        $query->bindValue(':filtro', "%$filtro%", PDO::PARAM_STR);
        $query->bindValue(':pag', $pag, PDO::PARAM_INT);
        $query->bindValue(':lim', $lim, PDO::PARAM_INT);

        try {
            $query->execute();
            $res = $query->fetchAll(PDO::FETCH_ASSOC);
            $status = count($res) > 0 ? 200 : 204;
        } catch (\PDOException $e) {
            $res = [];
            $status = 500;
        }

        $query = null;
        $con = null;

        $response->getBody()->write(json_encode($res));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}