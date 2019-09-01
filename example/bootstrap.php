<?php
declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use MVQN\HTTP\Slim\Middleware\Authentication\Authenticators\FixedAuthenticator;
use MVQN\HTTP\Slim\Middleware\Handlers\NotFoundHandler;
use MVQN\HTTP\Slim\Middleware\Handlers\UnauthorizedHandler;
use MVQN\HTTP\Slim\Middleware\Views\TwigView;
use MVQN\HTTP\Slim\Middleware\Routing\QueryStringRouter;

use MVQN\HTTP\Twig\Extensions\QueryStringRoutingExtension;
use MVQN\HTTP\Twig\Extensions\SwitchExtension;
use Slim\App;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Router;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;

$app = new App([
    "settings" => [
        "displayErrorDetails" => true,
        "addContentLengthHeader" => false,
        "determineRouteBeforeAppMiddleware" => true,
    ],
    "twig" => new TwigView(__DIR__."/views/"),
    "notFoundHandler" => new NotFoundHandler(),
    "unauthorizedHandler" => new UnauthorizedHandler(),
]);

$app->add(new FixedAuthenticator(false));
$app->add(new QueryStringRouter());