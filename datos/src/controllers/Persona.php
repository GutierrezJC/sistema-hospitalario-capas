<?php

namespace App\controllers;

use Psr\Container\ContainerInterface;

use PDO;

class Persona
{
    protected $container;


    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function createP($recurso, $rol, $datos)
    {
        $sql = "SELECT nuevo$recurso(";
        foreach ($datos as $key => $value) {
            $sql .= ":$key,";
        }
        $sql = substr($sql, 0, -1) . ");";
        reset($datos);
        $claveId = key($datos);

        $con =  $this->container->get('base_datos');
        $con->beginTransaction();
        $query = $con->prepare($sql);

        foreach ($datos as $key => $value) {
            $TIPO = gettype($value) == "integer" ? PDO::PARAM_INT : PDO::PARAM_STR;
            $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
            $query->bindValue($key, $value, $TIPO);
        };

        try {
            $query->execute();
            $res = $query->fetch(PDO::FETCH_NUM)[0];
            $status = match ($res) {
                0 => 201,
                1 => 409,
            };

            $identificacion = $datos['identificacion'] ?? null;
            //hasch
            $passw = password_hash($identificacion, PASSWORD_BCRYPT, ['cost' => 10]);

            // Si el recurso es Visitante o Administrador, también crea usuario
            if ($recurso === "Visitante" || $recurso === "Administrador" || $recurso === "visitante" || $recurso === "administrador") {
                $sql = "SELECT nuevoUsuario(:identificacion, :correo,  :rol, :passw);";
                $query = $con->prepare($sql);
                $query->bindValue(':identificacion', $identificacion, PDO::PARAM_STR);
                $query->bindValue(':correo', $datos['correo'], PDO::PARAM_STR);
                $query->bindValue(':rol', $rol, PDO::PARAM_STR); // Es VARCHAR(20) en la función
                $query->bindValue(':passw', $passw, PDO::PARAM_STR); // uso de pasww la contraseña 
                $query->execute();
                $query->fetch(PDO::FETCH_NUM)[0];
            }

            if ($status == 409) {
                $con->rollBack();
            } else {
                $con->commit();
            }
        } catch (\PDOException $e) {
            $status = 500;
            $con->rollBack();
        }


        $query = null;
        $con = null;
        return $status;
    }

    public function updateP($recurso, $datos, $identificacion)
    {
        $sql = "SELECT editar$recurso(:identificacion,";
        foreach ($datos as $key => $value) {
            $sql .= ":$key,";
        }
        $sql = substr($sql, 0, -1) . ");";

        $con =  $this->container->get('base_datos');
        $con->beginTransaction();
        $query = $con->prepare($sql);
        //var_dump($sql);
        

        $query->bindValue('identificacion', filter_var($identificacion, FILTER_SANITIZE_SPECIAL_CHARS), PDO::PARAM_STR);

        foreach ($datos as $key => $value) {
            $TIPO = gettype($value) == "integer" ? PDO::PARAM_INT : PDO::PARAM_STR;
            $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
            $query->bindValue($key, $value, $TIPO);
        };

        $status = 200;
        try {
            $query->execute();
            $con->commit();
            $res = $query->fetch(PDO::FETCH_NUM)[0];
            $status = match ($res) {
                1 => 404,
                0 => 200
            };
        } catch (\PDOException $e) {
            $status = $e->getCode() == 23000 ? 409 : 500;
            $con->rollBack();
        }

        $query = null;
        $con = null;

        return $status;
    }

    public function deleteP($recurso, $identificacion)
    {
        $sql = "SELECT eliminar$recurso(:identificacion);";
        $con =  $this->container->get('base_datos');
        $query = $con->prepare($sql);
        $query->bindValue('identificacion', $identificacion, PDO::PARAM_STR);
        $query->execute();
        $resp = $query->fetch(PDO::FETCH_NUM)[0];
        $query = null;
        $con = null;
        return $resp > 0 ? 200 : 404;
    }

    public function filtrarP($recurso, $datos, $pag, $lim)
    {
        $filtro = "%";
        foreach ($datos as $key => $value) {
            $filtro .= "$value%&%";
        }
        $filtro = substr($filtro, 0, -1);

        $sql = "CALL filtrar$recurso('$filtro', $pag, $lim);";
        $con =  $this->container->get('base_datos');
        $query = $con->prepare($sql);
        $query->execute();
        $res = $query->fetchAll();
        $status = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;
        return ["datos" => $res, "status" => $status];
    }

    public function buscarP($recurso, $id)
    {

        $sql = "CALL buscar$recurso($id,'');";

        $con =  $this->container->get('base_datos');
        $query = $con->prepare($sql);

        $query->execute();
        $res = $query->fetch(PDO::FETCH_ASSOC);
        $status = $query->rowCount() > 0 ? 200 : 204;

        $query = null;
        $con = null;

        return ["datos" => $res, "status" => $status];
    }
}
