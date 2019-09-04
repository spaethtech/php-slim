<?php
declare(strict_types=1);
require_once __DIR__ . "/../vendor/autoload.php";

use MVQN\HTTP\Slim\DefaultApp;
use MVQN\HTTP\Slim\Middleware\Views\TwigView;

use MVQN\HTTP\Slim\Middleware\Authentication\Authenticators\FixedAuthenticator;
use MVQN\HTTP\Slim\Middleware\Routing\QueryStringRouter;
use MVQN\HTTP\Twig\Extensions\QueryStringRoutingExtension;
use Slim\Views\Twig;

/** @noinspection PhpUnhandledExceptionInspection */
$app = new DefaultApp([

    "settings" => [
        // NOTE: Here we enable Slim's extra error details when in development mode.
        "displayErrorDetails" => true,
    ],

    // NOTE: We add the Twig instance here, as we need to set some values that will not be common to all applications!
    "twig" =>  new TwigView(
        // NOTE: This can be either a single path to the Templates or an array of multiple paths.
        __DIR__."/views/",

        // NOTE: Pass any desired options to be used during the initialization of the Twig Environment.
        [ "debug" => true ],

        // NOTE: Include any desired global values, which will be added to the "app.<name>" variable in Twig templates.
        [
            "baseUrl" => "http://localhost",
            "baseScript" => "/index.php",
        ]
    ),

    // NOTE: Add additional (or override) dependencies to the Container here...
]);

// NOTE: We can add additional global values at any time, but they will be overwritten by duplicates passed above!
//QueryStringRoutingExtension::addGlobal("baseScript", "/index.php");

$app->add(new FixedAuthenticator(false));
$app->add(new QueryStringRouter("/", ["#/public/#" => "/"]));