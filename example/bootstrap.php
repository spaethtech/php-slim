<?php
declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use MVQN\HTTP\Slim\DefaultApp;
use MVQN\HTTP\Slim\Middleware\Views\TwigView;

use MVQN\HTTP\Slim\Middleware\Authentication\Authenticators\FixedAuthenticator;
use MVQN\HTTP\Slim\Middleware\Routing\QueryStringRouter;

$app = new DefaultApp([
    "twig" => new TwigView(__DIR__."/views/"),
]);

$app->add(new FixedAuthenticator(false));
$app->add(new QueryStringRouter());