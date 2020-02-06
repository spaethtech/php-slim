<?php
declare(strict_types=1);
require_once __DIR__ . "/../vendor/autoload.php";

use Slim\Factory\AppFactory;
use MVQN\Slim\Middleware\Routing\QueryStringRouter;
use MVQN\Slim\Middleware\Authentication\Authenticators\FixedAuthenticator;

use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;
use MVQN\Slim\Middleware\Handlers\MethodNotAllowedHandler;
use MVQN\Slim\Middleware\Handlers\NotFoundHandler;
use MVQN\Slim\Middleware\Handlers\UnauthorizedHandler;



$app = AppFactory::create();

// Add Routing Middleware.
$app->addRoutingMiddleware();

$app->add(new QueryStringRouter("/", ["#/public/#" => "/"]));

// Add an application-level Authenticator.
$app->add(new FixedAuthenticator(true));

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

