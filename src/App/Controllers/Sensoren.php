<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\SensorRepository;
use Valitron\Validator;

class Sensoren
{
    public function __construct(private SensorRepository $repository,
                                private Validator $validator)
    {
        $this->validator->mapFieldsRules([
            'Type' => ['required'],
            'LocatieBeschrijving' => ['required'],
            'Diepte' => ['required']
        ]);
    }

    public function showAllSensoren(Request $request, Response $response): Response{
        $data = $this->repository->getAllSensoren();
    
        $body = json_encode($data);
    
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json');  
    }

    public function addSensor(Request $request, Response $response): Response{
        $body = $request->getParsedBody();
        
        $this->validator = $this->validator->withData($body); 

        if(!$this->validator->validate()) {
            $response->getBody() 
                    ->write(json_encode($this->validator->errors())); 
            return $response->withStatus(422);
        }

        $id = $this->repository->addSensor($body); 

        $body = json_encode([
            'message' => 'Sensor toegevoegd',
            'id' => $id
        ]);

        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function ShowSensorById(Request $request, Response $response, string $id): Response{
        $data = $this->repository->getSenorById((int) $id);

        if($data === false) {
            throw new \Slim\Exception\HttpNotFoundException($request,
                                                            message: "Sensor bestaat niet");
        }
        $body = json_encode($data);
    
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json');
    }
}