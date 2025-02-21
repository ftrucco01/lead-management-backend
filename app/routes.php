<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello from Lead Management Backend!');
        return $response;
    });

    // Database test
    $app->get('/db-test', function (Request $request, Response $response) use ($app) {
        /** @var \PDO $db */
        $db = $app->getContainer()->get('db');

        try {
            $stmt = $db->query('SELECT DATABASE() AS db_name');
            $result = $stmt->fetch();

            $message = $result && $result['db_name'] 
                ? "Connected to DB: {$result['db_name']}" 
                : "Not connected to any database.";

            $response->getBody()->write($message);
        } catch (\PDOException $e) {
            $response->getBody()->write("Database connection error: " . $e->getMessage());
        }

        return $response;
    });

};
