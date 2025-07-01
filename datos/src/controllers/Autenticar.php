<?php

namespace App\controllers;


use Psr\Container\ContainerInterface;

use PDO;

class Autenticar
{
    protected $container;
    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function autenticar($identificacion, $passw, $cambioPassw = false)
    {
        $sql = "SELECT * FROM usuario WHERE identificacion = :identificacion OR correo = :identificacion;";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        //en el bind value asocio con lo que esta entrando
        $query->bindValue(':identificacion', $identificacion, PDO::PARAM_STR);
        $query->execute();
        $datos = $query->fetch();
        if ($datos && password_verify($passw, $datos->passw)) {
            if (!$cambioPassw) {
                $retorno = ["rol" => $datos->rol];
                $recurso = match ((int)$datos->rol) {
                    1 => "administrador",
                    2 => "visitante",
                    default => "visitante"
                };
                
                // Si el rol es 1, se busca en la tabla administrador, si es 2, en visitante

                $sql = "SELECT nombre FROM $recurso WHERE identificacion= :identificacion ";
                $sql .= "OR correo = :identificacion;";
                $query = $con->prepare($sql);
                $query->bindValue(':identificacion', $datos->identificacion, PDO::PARAM_STR);
                $query->execute();
                $datos = $query->fetch();
                $retorno['nombre'] = $datos->nombre;
            } else {
                $retorno = true;
            }
        }
        $query = null;
        $con = null;
        return isset($retorno) ? $retorno : null;
    }


    public function autenticarEndpoint($request, $response, $args)
    {
        $body = json_decode($request->getBody(), true);
        $idUsuario = $body['identificacion'] ?? '';
        $passw = $body['passw'] ?? '';

        // Llama a tu método original
        $resultado = $this->autenticar($idUsuario, $passw);

        $status = $resultado ? 200 : 401;
        $response->getBody()->write(json_encode($resultado ?: ["error" => "Credenciales incorrectas"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
