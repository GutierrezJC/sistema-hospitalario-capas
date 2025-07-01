<?php

use Psr\Container\ContainerInterface;

// Configura el servicio 'base_datos' en el contenedor de dependencias.
// Este servicio devuelve una instancia de PDO conectada a la base de datos MySQL.
$container ->set('base_datos',function(ContainerInterface $c) {
    // Obtiene la configuración de la base de datos desde el contenedor
    $conf = $c->get('config_bd');

    // Opciones para la conexión PDO: manejo de errores y modo de obtención de resultados
    $opc =[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];
    // Construye el DSN para la conexión MySQL
    $dsn = "mysql:host=$conf->host;dbname=$conf->db;charset=$conf->charset";

    try {
        // Intenta crear una nueva conexión PDO
        $con = new PDO($dsn,$conf->usr,$conf->passw,$opc);
        //die("Conectado a la base de datos");

    } catch (PDOException $e) {
        // Si ocurre un error, muestra el mensaje y detiene la ejecución
        print ('Error de conexión: ' . $e->getMessage(). '<br>');
        die("Error conectando a la base de datos");
    }
    
    // Devuelve la conexión PDO lista para usarse
    return $con;
});