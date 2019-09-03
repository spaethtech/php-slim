<?php
declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/bootstrap.php";

use MVQN\HTTP\Slim\Middleware\Authentication\AuthenticationHandler;
use MVQN\HTTP\Slim\Middleware\Authentication\Authenticators\FixedAuthenticator;
use Slim\Http\Request;
use Slim\Http\Response;

use MVQN\HTTP\Slim\Routes;
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

    // NOTE: This Controller handles any static assets (i.e. png, jpg, html, pdf, etc.)...
    (new Routes\AssetRoute(
        $app,
        __DIR__."/assets/",
        // NOTE: If one or more Authenticators are provided, they will override the application-level Authenticator(s).
    ))->add(new AuthenticationHandler($container))->add(new FixedAuthenticator(true));

    // NOTE: This Controller handles any Twig templates...
    (new Routes\TemplateRoute(
        $app,
        __DIR__."/views/",
        // NOTE: Here we can declare null to remove any Authenticator(s), including application-level Authenticator(s).
        //null
    ));

    // NOTE: This Controller handles any PHP scripts...
    (new Routes\ScriptRoute(
        $app,
        __DIR__."/src/"
        // NOTE: Or simply omit the parameter to use any application-level Authenticator(s).
    ));





    $app->get("/",

        function ( /** @noinspection PhpUnusedParameterInspection */ Request $request, Response $response, array $args)
        {
            // NOTE: Inside any route closure, $this refers to the Application's Container.
            return $response->write(file_get_contents(__DIR__ . "/assets/index.html"));
        }

    );//->add(new AuthenticationHandler($container))->add(new TestsAuthenticator());

    // Custom route using an inline method...
    // NOTE: Notice this more specific route takes precedence over the previous /example route.
    $app->get("/example[/[{name}]]",

        function ( /** @noinspection PhpUnusedParameterInspection */ Request $request, Response $response, array $args)
        {
            // NOTE: Inside any route closure, $this refers to the Application's Container.
            return $response->withJson([ "name" => $args["name"] ?? "", "description" => "This is an example JSON route!" ]);
        }

    )->add(new AuthenticationHandler($container))->add(new FixedAuthenticator(true));




    $app->get("/authorized",

        function ( /** @noinspection PhpUnusedParameterInspection */ Request $request, Response $response, array $args)
        {
            // NOTE: Inside any route closure, $this refers to the Application's Container.
            return $response->write("This content should ALWAYS be displayed!");
        }

    )->add(new AuthenticationHandler($container))->add(new FixedAuthenticator(true));

    $app->get("/unauthorized",

        function ( /** @noinspection PhpUnusedParameterInspection */ Request $request, Response $response, array $args)
        {
            // NOTE: Inside any route closure, $this refers to the Application's Container.
            return $response->write("This content should NEVER be displayed!");
        }

    )->add(new AuthenticationHandler($container))->add(new FixedAuthenticator(false));

    // =================================================================================================================
    // APPLICATION EXECUTION
    // =================================================================================================================

    // Run the Slim Framework Application!
    $app->run();

})();

