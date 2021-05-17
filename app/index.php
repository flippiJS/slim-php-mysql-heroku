<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/AutentificadorJWT.php';

require_once './controllers/UsuarioController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);


// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
});

// JWT test routes
$app->group('/jwt', function (RouteCollectorProxy $group) {

  $group->post('/crearToken', function (Request $request, Response $response) {    
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $perfil = $parametros['perfil'];
    $alias = $parametros['alias'];

    $datos = array('usuario' => $usuario, 'perfil' => $perfil, 'alias' => $alias);

    $token = AutentificadorJWT::CrearToken($datos);
    $payload = json_encode(array('jwt' => $token));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->post('/devolverPayLoad', function (Request $request, Response $response) {
    $parametros = $request->getParsedBody();
    $token = $parametros['token'];

    try {
      $payload = json_encode(array('payload' => AutentificadorJWT::ObtenerPayLoad($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->post('/devolverDatos', function (Request $request, Response $response) {
    $parametros = $request->getParsedBody();
    $token = $parametros['token'];

    try {
      $payload = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->post('/verificarToken', function (Request $request, Response $response) {
    $parametros = $request->getParsedBody();
    $token = $parametros['token'];
    $esValido = false;

    try {
      AutentificadorJWT::verificarToken($token);
      $esValido = true;
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if ($esValido) {
      $payload = json_encode(array('valid' => $esValido));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });
});


$app->get('[/]', function (Request $request, Response $response) {
  $response->getBody()->write("Slim Framework 4 PHP");
  return $response;
});

$app->run();
