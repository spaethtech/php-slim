<?php
declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/bootstrap.php";

use MVQN\HTTP\Slim\Middleware\Authentication\AuthenticationHandler;
use MVQN\HTTP\Slim\Middleware\Authentication\Authenticators\CallbackAuthenticator;
use MVQN\HTTP\Slim\Middleware\Authentication\Authenticators\FixedAuthenticator;
use Slim\Http\Request;
use Slim\Http\Response;

use MVQN\HTTP\Slim\Controllers;
use Slim\Route;

/**
 * Use an immediately invoked function here, to avoid global namespace pollution...
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 */
(function() use ($app)
{
    $container = $app->getContainer();

    // NOTE: You can include any valid route syntax supported by the Slim Framework.  All routes and controllers placed
    // here will override any built-in controllers added below.  This is the perfect location to place API (server-side)
    // routes for consumption by the client-side code provided by VueJS in this Plugin.

    // TODO: Add additional custom routes or controllers here!
    // ...



    // =================================================================================================================
    // BUILT-IN ROUTES
    // NOTE: These controllers should be added last, so the above controllers can override routes as needed.
    // =================================================================================================================

    // Append a route handler for static assets.
    new Controllers\AssetController($app, __DIR__."/assets/", new FixedAuthenticator(false));
    // NOTE: This will load static assets (i.e. png, jpg, html, pdf, etc.)

    // Append a route handler for Twig templates.
    new Controllers\TemplateController($app, __DIR__."/views/");

    // Append a route handler for PHP scripts.
    new Controllers\ScriptController($app, __DIR__."/src/");





    $app->get("/",

        function ( /** @noinspection PhpUnusedParameterInspection */ Request $request, Response $response, array $args)
        use ($container)
        {
            return $response->write(file_get_contents(__DIR__ . "/index.html"));
        }

    );

    // Custom route using an inline method...
    // NOTE: Notice this more specific route takes precedence over the previous /example route.
    $app->get("/example[/[{name}]]",

        function ( /** @noinspection PhpUnusedParameterInspection */ Request $request, Response $response, array $args)
        use ($container)
        {
            return $response->withJson([ "name" => $args["name"] ?? "", "description" => "This is an example JSON route!" ]);
        }

    )->add(new AuthenticationHandler($container))->add(new FixedAuthenticator(true));

    // =================================================================================================================
    // APPLICATION EXECUTION
    // =================================================================================================================

    // Run the Slim Framework Application!
    $app->run();

})();

