<?php
declare(strict_types=1);

namespace MVQN\Slim\Controllers;

use MVQN\Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteGroup;

/**
 * An abstract Controller class, from which to extend all desired Controllers.
 *
 * NOTE: Controllers can only be added directly to Applications and can not currently be part of a RouteGroup, as they
 * are special RouteGroups themselves.
 *
 * @package MVQN\Slim\Controllers
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @copyright 2020 Spaeth Technologies, Inc.
 */
abstract class Controller extends RouteCollectorProxy implements RouteCollectorProxyInterface
{
    /**
     * Controller constructor.
     *
     * @param App $app The Application to which this Controller belongs.
     * @param string $pattern An optional {@see RouteGroup} pattern to use for this controller, defaults to "".
     */
    public function __construct(App $app, string $pattern = "")
    {
        parent::__construct(
            $app->getResponseFactory(),
            $app->getCallableResolver(),
            $app->getContainer(),
            $app->getRouteCollector(),
            $pattern
        );

    }

    /**
     * @param App $app The Application to which this Controller belongs.
     *
     * @return RouteGroupInterface Returns a RouteGroup for further chaining.
     */
    public abstract function __invoke(App $app): RouteGroupInterface;

}
