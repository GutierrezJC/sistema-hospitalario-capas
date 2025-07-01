<?php
// Habilitar mostrar errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Importa la clase AppFactory de Slim para crear la aplicación
use Slim\Factory\AppFactory;

// Importa el contenedor de dependencias de PHP-DI
use DI\Container;

// Carga el autoloader de Composer para gestionar dependencias recordar aqui estoy en app osea me devulevo 2 
require __DIR__ . '/../../vendor/autoload.php';
// traeer las variables de entorno 
$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html');
$dotenv->load(); // Carga las variables de entorno desde el archivo .env

// Crea una nueva instancia del contenedor de dependencias
$container = new Container();

// Asigna el contenedor a la aplicación Slim
AppFactory::SetContainer($container);

// Crea la instancia principal de la aplicación Slim
$app = AppFactory::create();

// Carga la configuración de la aplicación
require "config.php";
// Carga la configuración de la conexión a la base de datos


// recordar que esta es la parte que impide que se pueda acceder a las rutas de la api sin un token
//"ignore" => ["/api/auth", "/api/visitante", "/api/administrador", "/api/usuario","/api/visita","/api/user"]
//esta clse es la que tiene el jwt la autorizacion ( se enecarga de bloquearme a mi lo que puedo  no usar )
// ,"/api/visita", "/api/visitante"   "/api/visitante", "/api/administrador"
$app->add(new Tuupola\Middleware\JwtAuthentication([
    "secure" => false,
    "path" => ["/api"], // rutas que van a estar bloquedas o protegidas despues de api todo esta bloqueado si hay un token 
    "ignore" => ["/api/auth"], // el ingnore es para que no bloqueo para entrar a la ruta de autenticacion
    "secret" => ["acme" => $container->get('key')], // la llave para decodificar el jwt
    "algorithm" => ["acme" => "HS256"], // el tipo de algoritmo para decodificar el jwt
]));



require "conexion.php";
// Carga las rutas de la API o aplicación
require "routes.php";

// Ejecuta la aplicación Slim (inicia el servidor y gestiona las peticiones)
$app->run();
