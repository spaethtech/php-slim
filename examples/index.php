<?php
/** @noinspection PhpUnusedParameterInspection */
declare(strict_types=1);
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/bootstrap.php";

use rspaeth\Slim\Controllers\ExampleController;
use rspaeth\Slim\Middleware\Authentication\AuthenticationHandler;
use rspaeth\Slim\Middleware\Authentication\Authenticators\CallbackAuthenticator;
use rspaeth\Slim\Middleware\Authentication\Authenticators\FixedAuthenticator;
use rspaeth\Slim\Psr7\Http\Message\JsonResponse;
use rspaeth\Slim\Controllers\AssetController;
use rspaeth\Slim\Controllers\ScriptController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Use an immediately invoked function here, to avoid global namespace pollution...
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @copyright 2020 Spaeth Technologies, Inc.
 */

// NOTE: This Controller handles any static assets (i.e. png, jpg, html, pdf, etc.)...
$app->addController(new AssetController($app, __DIR__ . "/assets/"))
    ->addMiddleware(new AuthenticationHandler($app))
    ->addMiddleware(new FixedAuthenticator(true));

// NOTE: This Controller handles any PHP scripts...
$app->addController(new ScriptController($app, __DIR__ . "/scripts/"))
    ->addMiddleware(new AuthenticationHandler($app))
    ->addMiddleware(new FixedAuthenticator(false));

$app->addController(new ExampleController($app));
//$app->get("/date", [ new ExampleController($app), "date"]);


// Define app routes
$app->get('/hello/{name}', function (Request $request, Response $response, $args): Response {
    $name = $args['name'];
    $data = [ "name" => $name, "message" => "This is a JSON test!" ];
    return JsonResponse::fromResponse($response, $data);
    //return JsonResponse::create($data);
})
    ->add(new AuthenticationHandler($app));
    /*
    ->add(new CallbackAuthenticator(
        function(Request $request): bool
        {
            return true;
        }
    ));
    */

$app->map(["post", "patch"],"/test", function (Request $request, Response $response, $args): Response {
    $response->getBody()->write("TEST");
    return $response;
});

$app->get("[/]", function (Request $request, Response $response, $args): Response {
    $response->getBody()->write("HOME");
    return $response;
})->setName("home");

// Run app
$app->run();
