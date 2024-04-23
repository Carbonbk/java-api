<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\MetingRepository;
use Valitron\Validator;

class Metingen
{
    public function __construct(private MetingRepository $repository,
                                private Validator $validator)
    {
        $this->validator->mapFieldsRules([
            'SensorID'=>['required'],
            'Waarde'  =>['required']
        ]);
    }

    public function ShowAllMetingen(Request $request, Response $response): Response{
        $data = $this->repository->getAllMetingen();

        $body = json_encode($data);
    
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ShowMetingById(Request $request, Response $response, string $id): Response{
        $data = $this->repository->getMetingById((int) $id);
        
        if($data === false) {
        throw new \Slim\Exception\HttpNotFoundException($request,
                                                        message: "Meting bestaat niet");
        }
        $body = json_encode($data);

        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function addMeting(Request $request, Response $response): Response {     
        $body = $request->getParsedBody();
        
        $this->validator = $this->validator->withData($body); 

        if(!$this->validator->validate()) {
            $response->getBody() 
                    ->write(json_encode($this->validator->errors())); 
            return $response->withStatus(422);
        }

        $id = $this->repository->addMeting($body); 

        $body = json_encode([
            'message' => 'Meting toegevoegd',
            'id' => $id
        ]);

        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function getFilteredMetingen(Request $request, Response $response): Response{
        $queryParams = $request->getQueryParams();
        
        $data = $this->repository->getFilteredMetingen($queryParams);

        $body = json_encode($data);
    
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json');
    }
}