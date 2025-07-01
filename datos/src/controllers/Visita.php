<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;
use PDOException;

class Visita
{
    protected $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function read(Request $request, Response $response, $args)
    {
        $sql = "SELECT * FROM visita ";
        if (isset($args['id_visita'])) {
            $sql .= "WHERE id_visita = :id_visita ";
        }
        $sql .= "LIMIT 0,5;";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);

        if (isset($args['id_visita'])) {
            $query->execute(['id_visita' => $args['id_visita']]);
        } else {
            $query->execute();
        }

        $res = $query->fetchAll();
        $status = $query->rowCount() > 0 ? 200 : 204;

        $query = null;
        $con = null;

        $response->getBody()->write(json_encode($res));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public function create(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody(), true);

        $sql = "SELECT nuevaVisita(:identificacion_visitante, :identificacion_administrador, :motivo_visita, :fecha_entrada, :fecha_salida, :estado) AS resultado;";

        $con = $this->container->get('base_datos');
        $con->beginTransaction();
        $query = $con->prepare($sql);


        // foreach ($body as $key => $value) {
        //     $TIPO = gettype($value) == "integer" ? PDO::PARAM_INT : PDO::PARAM_STR;
        //     $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        //     $query->bindValue($key, $value, $TIPO);
        // }
        foreach ($body as $key => $value) {
            if (is_null($value)) {
                $query->bindValue($key, null, PDO::PARAM_NULL);
            } else {
                $TIPO = gettype($value) == "integer" ? PDO::PARAM_INT : PDO::PARAM_STR;
                $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                $query->bindValue($key, $value, $TIPO);
            }
        }

        try {
            $query->execute();
            $res = $query->fetch(PDO::FETCH_ASSOC)['resultado'];

            $status = match ($res) {
                0 => 201, // Creado
                1 => 409, // Ya existe
                default => 500
            };
            if ($status == 409) {
                $con->rollBack();
            } else {
                $con->commit();
            }
        } catch (PDOException $e) {
            $status = 500;
            $con->rollBack();
        }

        $query = null;
        $con = null;


        return $response->withStatus($status);
    }

    public function update(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody(), true);

        $sql = "SELECT editarVisita(:id_visita, :identificacion_visitante, :identificacion_administrador, :motivo_visita, :fecha_entrada, :fecha_salida, :estado) AS resultado;";

        $con = $this->container->get('base_datos');
        $con->beginTransaction();
        $query = $con->prepare($sql);

        $id_visita = filter_var($args['id_visita'], FILTER_SANITIZE_NUMBER_INT);
        $query->bindValue('id_visita', $id_visita, PDO::PARAM_INT);

        foreach ($body as $key => $value) {
            $TIPO = gettype($value) == "integer" ? PDO::PARAM_INT : PDO::PARAM_STR;
            $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
            $query->bindValue($key, $value, $TIPO);
        }

        try {
            $query->execute();
            $res = $query->fetch(PDO::FETCH_ASSOC)['resultado'];
            $status = match ($res) {
                0 => 200, // Editado
                1 => 404, // No encontrado
                default => 500
            };
            $con->commit();
        } catch (PDOException $e) {
            $status = 500;
            $con->rollBack();
        }

        $query = null;
        $con = null;

        return $response->withStatus($status);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $sql = "SELECT eliminarVisita(:id_visita) AS resultado;";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);

        $id_visita = filter_var($args['id_visita'], FILTER_SANITIZE_NUMBER_INT);
        $query->bindValue('id_visita', $id_visita, PDO::PARAM_INT);
        $query->execute();

        $resp = $query->fetch(PDO::FETCH_ASSOC)['resultado'];
        $status = $resp > 0 ? 200 : 404;

        $query = null;
        $con = null;

        return $response->withStatus($status);
    }

    public function filtrar(Request $request, Response $response, $args)
    {
        // %identificacion_visitante%&%identificacion_administrador%&%motivo_visita%&%estado%&%fecha_entrada
        $datos = $request->getQueryParams();
        $filtro = "%";
        foreach ($datos as $key => $value) {
            $filtro .= "$value%&%";
        }
        $filtro = substr($filtro, 0, -1);

        $sql = "CALL filtrarVisita('$filtro', {$args['pag']}, {$args['lim']});";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);

        $query->execute();
        $res = $query->fetchAll();
        $status = $query->rowCount() > 0 ? 200 : 204;

        $query = null;
        $con = null;

        $response->getBody()->write(json_encode($res));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }


    public function filtrarNuevo(Request $request, Response $response, $args)
    {
        $datos = $request->getQueryParams();
        $filtro = "%";
        foreach ($datos as $key => $value) {
            $filtro .= "$value%&%";
        }
        $filtro = substr($filtro, 0, -1);

        // Valores fijos para pag y lim
        $pag = 0;
        $lim = 10;

        $sql = "CALL filtrarfVisita('$filtro', $pag, $lim);";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);

        $query->execute();
        $res = $query->fetchAll();
        $status = $query->rowCount() > 0 ? 200 : 204;

        $query = null;
        $con = null;


        $response->getBody()->write(json_encode($res));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }



    public function buscarVisita(Request $request, Response $response, $args)
    {
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        $sql = "CALL buscarVisita(:id);";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);

        $query->bindValue('id', $id, PDO::PARAM_INT);
        $query->execute();
        $res = $query->fetch(PDO::FETCH_ASSOC);
        $status = $query->rowCount() > 0 ? 200 : 204;

        $query = null;
        $con = null;

        $response->getBody()->write(json_encode([ $res ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
