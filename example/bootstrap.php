<?php
declare(strict_types=1);
require_once __DIR__ . "/../vendor/autoload.php";

use DI\Container;
use MVQN\Slim\Middleware\Authentication\AuthenticationHandler;
use Slim\Factory\AppFactory;
use MVQN\Slim\Middleware\Routing\QueryStringRouter;
use MVQN\Slim\Middleware\Authentication\Authenticators\FixedAuthenticator;

use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;
use MVQN\Slim\Middleware\Handlers\MethodNotAllowedHandler;
use MVQN\Slim\Middleware\Handlers\NotFoundHandler;
use MVQN\Slim\Middleware\Handlers\UnauthorizedHandler;

//use Slim\Views\Twig;
//use Slim\Views\TwigMiddleware;


AppFactory::setContainer($container = new Container());
$app = AppFactory::create();

// Necessary for injection of the base App, as a ResponseFactory is required to function properly.
$container->set(\Psr\Http\Message\ResponseFactoryInterface::class, DI\create(\Slim\Psr7\Factory\ResponseFactory::class));

// Add Routing Middleware.
$app->addRoutingMiddleware();

// Add an application-level Authenticator.
$app->add(new FixedAuthenticator(true));



/*
$container->set("twig", function() {

    //$twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader([ realpath(__DIR__."/views/") ]),[ "cache" => realpath(__DIR__."/views/.cache/") ]);
    $twig = Twig::create([ realpath(__DIR__."/views/") ], [ "cache" => realpath(__DIR__."/views/.cache/") ]);


    $twig->getEnvironment()->addGlobal("home", "/index.php");

    return $twig;
});

TwigMiddleware::createFromContainer($app, "twig");//, Twig::class);
*/



$app->add(new QueryStringRouter("/", ["#/public/#" => "/"]));






/**
 * Add Error Handling Middleware
 *
 * @param bool $displayErrorDetails Should be set to false in production
 * @param bool $logErrors Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails Display error details in error log which can be replaced by a callable of your choice.

 * Note: This middleware should be added last, as it will not handle any exceptions/errors for anything added after it!
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(HttpUnauthorizedException::class, new UnauthorizedHandler($app)); // 401
$errorMiddleware->setErrorHandler(HttpNotFoundException::class, new NotFoundHandler($app)); // 404
$errorMiddleware->setErrorHandler(HttpMethodNotAllowedException::class, new MethodNotAllowedHandler($app)); // 405

