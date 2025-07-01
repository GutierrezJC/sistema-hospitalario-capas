<?php
namespace App\controllers; // lo defino como App\controllers para que quede en la carpeta controllers
// iniciar 
// PARA PROBAR PRIMERO CREO EL CLIENTE CON EL APP COMENTADO Y LUEGO HAGO INICIAR INSOMIA OSEA EL QUE NO TIENE NADA 
// ENVIO Y EL ME DEVUELVE DOS TOKENS UNO DEL USUARIO Y OTRO DE REFERENCIA



//refrescar 
// mando el json, CON el idusuario y el token de referencia en el cuerpo ( con comilllas dobles porque es un string )
// ( recordar que una vez lo actualizo tengo que cambiar con la informacion nueva para que no de error )
// y el aut el token pro defecto temporal ( sin comillas) y solo sirve una vez PREGRUNTA A HENY 

// cerrar
// mando el id usuario en la url y ademas mando el token NORMAL ES DEDIIR MANDOR EL QUE NO ES DE REFERENCIA EL DE REFERENCIA NO SE ULITIZA SLO PARA REFRESCAR en el auth y revezar en la bd que el tkf este vacio 

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use PDO;

class Auth extends Autenticar
{
    protected $container;
    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    private function accederToken(string $proc, string $identificacion, string $tkRef = "")
    {
        // depende lo que se llamo o proc o modificar o lo verifica
        $sql = $proc == "modificar" ? "SELECT modificarToken " : "CALL verificarTokenR ";
        $sql .= "(:identificacion, :tkRef);"; // como el tfre esta vacio en el sql entonces el update es vacio 
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        $query->execute([
            'identificacion' => $identificacion,
            'tkRef' => $tkRef
        ]);
        $datos = $proc == "modificar" ? $query->fetchAll(PDO::FETCH_OBJ) : $query->fetchColumn();// si es modificar me devuelve un array de objetos y si es verificar me devuelve un string
        $query = null;
        $con = null;
        return $datos;
    }

    private function modificarToken(string $identificacion, string $tkRef = "")
    {
        return $this->accederToken("modificar", $identificacion, $tkRef);// ambos acceden pero lo que cambia es el procedimiento almacenado que se llama
    }

    private function verificarToken(string $identificacion, string $tkRef)
    {
        return $this->accederToken("verificar", $identificacion, $tkRef);
    }

    private function generarToken(string $identificacion, string $rol, string $nombre)
    {
        // defino las propeidesdes que va tenerl el token en particular 
        $key = $this->container->get('key'); // Clave secreta para firmar el token  la saco del contenedor
        $payload = [
            'iss' => $_SERVER['SERVER_NAME'], // Emisor del token osea quien lo genera 
            'iat' => time(),
            'exp' => time() + 3600, // 1 hora de validez son 3600
            'sub' => $identificacion, // Asunto del token todo esta conetiddo dentro del token
            'rol' => $rol, // Rol del usuario
            'nom' => $nombre // Nombre del usuario
        ];

        $payloadRef = [
            'iss' => $_SERVER['SERVER_NAME'], // Emisor del token
            'iat' => time(),
            'rol' => $rol, // Rol del usuario
            'nom' => $nombre // Nombre del usuario
        ];

        // AQUI ES DONDE NOMBRO COMO SE LLLAMAN LOS TOKENS 
        return [
            "token" => JWT::encode($payload, $key, 'HS256'),
            "tkRef" => JWT::encode($payloadRef, $key, 'HS256') // Token de referencia
        ];

    }

    public function iniciar(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());// sacar ususario y contraseña del body
        if ($datos = $this->autenticar($body->identificacion, $body->passw)) {// osea si devolvio algo es porque el usuario y contraseña son correctos
            // Si la autenticación es exitosa, generamos el token
            $tokens = $this->generarToken($body->identificacion, $datos['rol'], $datos['nombre']);
            $this->modificarToken(identificacion: $body->identificacion, tkRef: $tokens['tkRef']);
            $response->getBody()->write(json_encode($tokens));
            $status = 200; // OK
        } else {
            $status = 401; // No autorizado
        }
        return $response->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    public function cerrar(Request $request, Response $response, $args)
    {
        $this->modificarToken(identificacion: $args['identificacion']);
        return $response->withStatus(200); // OK
    }

    public function refrescar(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());
        $rol = $this->verificarToken(identificacion: $body->identificacion, tkRef: $body->tkRef);
        $status = 200;
        if ($rol) {
            $datos = JWT::decode($body->tkRef, new Key($this->container->get('key'), 'HS256'));
            $tokens = $this->generarToken($body->identificacion, $datos->rol, $datos->nom);
            $this->modificarToken(identificacion: $body->identificacion, tkRef: $tokens['tkRef']);
            $response->getBody()->write(json_encode($tokens));
        } else {
            $status = 401; // No autorizado
        }
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}