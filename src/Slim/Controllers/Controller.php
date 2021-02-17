<?php
declare(strict_types=1);

namespace MVQN\Slim\Controllers;

use MVQN\Slim\Application;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteGroup;

/**
 * An abstract Controller class, from which to extend all other Controllers.
 *
 * _NOTE: Controllers can only be added directly to an {@see Application} and can not be part of a {@see RouteGroup},
 * as they are special {@see RouteGroup}s themselves._
 *
 * @package MVQN\Slim\Controllers
 *
 * @author Ryan Spaeth
 * @copyright 2020 Spaeth Technologies, Inc.
 */
abstract class Controller extends RouteCollectorProxy implements RouteCollectorProxyInterface
{
    /**
     * Controller constructor.
     *
     * @param Application $app The {@see Application} to which this Controller belongs.
     * @param string $prefix An optional {@see RouteGroup} prefix to use for this Controller, defaults to "".
     */
    public function __construct(Application $app, string $prefix = "")
    {
        parent::__construct(
            $app->getResponseFactory(),
            $app->getCallableResolver(),
            $app->getContainer(),
            $app->getRouteCollector(),
            $prefix
        );

    }

    /**
     * @param Application $app The {@see Application} to which this Controller belongs.
     *
     * @return RouteGroupInterface Returns a {@see RouteGroup} for method chaining.
     */
    public abstract function __invoke(Application $app): RouteGroupInterface;

}
