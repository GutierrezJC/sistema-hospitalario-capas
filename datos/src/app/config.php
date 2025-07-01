<?php
// Configura los parámetros de conexión a la base de datos en el contenedor de dependencias.
// Devuelve un objeto con los datos necesarios para la conexión.
$container->set('config_bd',function(){
    return (object)[
        "host"   => "db",         // Host de la base de datos (nombre del servicio Docker)
        "db"     => "hospital",     // Nombre de la base de datos
        "usr"    => "root",       // Usuario de la base de datos
        "passw"  => "123456",     // Contraseña del usuario
        "charset"=> "utf8mb4"     // Codificación de caracteres
    ];
});

$container->set('key', function() {
    return $_ENV["KEY"];
});