<?php
/** @noinspection PhpUnusedParameterInspection */
declare(strict_types=1);
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * @author Ryan Spaeth
 * @copyright 2020 Spaeth Technologies, Inc.
 */

use App\Controllers\ExampleController;
use MVQN\Slim\Middleware\Authentication\AuthenticationHandler;
use MVQN\Slim\Middleware\Authentication\Authenticators\CallbackAuthenticator;
use MVQN\Slim\Middleware\Authentication\Authenticators\FixedAuthenticator;
use MVQN\Slim\Psr7\Http\Message\JsonResponse;
use MVQN\Slim\Controllers\AssetController;
use MVQN\Slim\Controllers\ScriptController;
use MVQN\Slim\App;
use MVQN\Slim\Middleware\Handlers\MethodNotAllowedHandler;
use MVQN\Slim\Middleware\Handlers\NotFoundHandler;
use MVQN\Slim\Middleware\Handlers\UnauthorizedHandler;

use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Create a new instance of a Slim 4 Application.
$app = new App();

// Add Routing Middleware, which is necessary for any routing.
$app->addRoutingMiddleware();

// Here we add our custom Query-based router and specify the following:
// - An optional default route, when none is provided.
// - An optional RegEx to assist with URL Rewrites, when desired.
$app->addQueryStringRoutingMiddleware("/", ["#/public/#" => "/"]);

/**
 * Add our custom Error Handling Middleware.
 *
 * @param bool $displayErrorDetails Should be set to false in production, as to prevent route information being leaked.
 * @param bool $logErrors Parameter is passed to the default ErrorHandler.
 * @param bool $logErrorDetails Display error details in error log which can be replaced by a callable of your choice.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(HttpUnauthorizedException::class, new UnauthorizedHandler($app)); // 401
$errorMiddleware->setErrorHandler(HttpNotFoundException::class, new NotFoundHandler($app)); // 404
$errorMiddleware->setErrorHandler(HttpMethodNotAllowedException::class, new MethodNotAllowedHandler($app)); // 405

// Add an application-level Authenticator.
$app->addAuthenticator(new FixedAuthenticator(true));

// This Controller handles any static assets (i.e. png, jpg, html, pdf, etc.)...
$app->addController(new AssetController($app, __DIR__ . "/assets/"))
    ->addMiddleware(new AuthenticationHandler($app)); // Uses Application-Level Authentication

// This Controller handles any PHP scripts...
$app->addController(new ScriptController($app, __DIR__ . "/scripts/"))
    ->addMiddleware(new AuthenticationHandler($app))
    ->addMiddleware(new FixedAuthenticator(true)); // Uses Controller-level Authentication

// This is a custom Controller included with the examples and uses NO Authentication.
$app->addController(new ExampleController($app));

// This is a hard-coded, but dynamic route...
$app->get('/hello/{name}', function (Request $request, Response $response, $args): Response {
    $name = $args['name'];
    $data = [ "name" => $name, "message" => "This is a JSON test!" ];

    // Here we return JSON using our custom PSR-7 ResponseFactory.
    return JsonResponse::fromResponse($response, $data);
})
    ->add(new AuthenticationHandler($app)) // Uses Route-Level Authentication...
    ->add(new CallbackAuthenticator( // ...and our custom CallbackAuthenticator.
        function(Request $request): bool
        {
            // Perform any desired logic here to determine Authentication.
            return true;
        }
    ));

// This maps a hard-coded route that supports both POST and PATCH...
$app->map(["post", "patch"],"/test", function (Request $request, Response $response, $args): Response {
    $response->getBody()->write("TEST");
    return $response;
});

// This maps a hard-coded route to a callable...
$app->get("/date", [ ExampleController::class, "date" ]);

// This is our default route...
$app->get("[/]", function (Request $request, Response $response, $args): Response {
    $response->getBody()->write("HOME");
    return $response;
})->setName("home"); // Using a named alias.

// Finally...we run our application!
$app->run();
