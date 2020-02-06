<?php
/** @noinspection PhpUnusedParameterInspection */
declare(strict_types=1);
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/bootstrap.php";

use MVQN\Slim\Middleware\Authentication\AuthenticationHandler;
use MVQN\Slim\Middleware\Authentication\Authenticators\CallbackAuthenticator;
use MVQN\Slim\Psr7\Http\Message\JsonResponse;
use MVQN\Slim\Routes\AssetRoute;
use MVQN\Slim\Routes\ScriptRoute;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Use an immediately invoked function here, to avoid global namespace pollution...
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */



// NOTE: This Controller handles any static assets (i.e. png, jpg, html, pdf, etc.)...
(new AssetRoute($app, __DIR__."/assets/"));

// NOTE: This Controller handles any PHP scripts...
(new ScriptRoute($app, __DIR__."/src/"));

// Define app routes
$app->get('/hello/{name}', function (Request $request, Response $response, $args): Response {
    $name = $args['name'];
    $data = [ "name" => $name, "message" => "This is a JSON test!" ];
    //return JsonResponse::fromResponse($response, $data);

    return new JsonResponse($response, $data);

})
    ->add(new AuthenticationHandler($app))
    ->add(new CallbackAuthenticator(
        function(Request $request): bool
        {
            return true;
        }
    ));

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
