<?php 

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class ApiKeyMiddleware
{
    private $apiKey = "Shoarma2";

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $apiHeader = $request->getHeaderLine('Authorization');

        if ($apiHeader !== $this->apiKey)
        {
            $response = new \Slim\Psr7\Response();

            $error = ['error' => '401 Unauthorized',
                      'exception' => [['type' => get_class($this),
                                        'code' => 401,
                                        'message' => 'Unauthorized',
                                        'file' => __FILE__,
                                        'line' => __LINE__]]];
            
            $response->getBody()->write(json_encode($error));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        return $handler->handle($request);
    }
}