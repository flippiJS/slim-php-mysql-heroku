<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class LoggerMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        // Fecha antes
        $before = date('Y-m-d H:i:s');
        
        // Continua al controller
        $response = $handler->handle($request);
        $existingContent = json_decode($response->getBody());
    
        // Despues
        $response = new Response();
        $existingContent->fechaAntes = $before;
        $existingContent->fechaDespues = date('Y-m-d H:i:s');
        
        $payload = json_encode($existingContent);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
