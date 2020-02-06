<?php
declare(strict_types=1);

namespace MVQN\Slim\Routes;

use Psr\Http\Server\MiddlewareInterface;
use Slim\Interfaces\RouteInterface;

/**
 * An abstract Route class, from which any Route package
 *
 * @package MVQN\Slim\Routes
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @copyright 2020 Spaeth Technologies, Inc.
 */
abstract class BuiltInRoute
{

    /** @var RouteInterface */
    protected $route;

    /**
     * @param callable|MiddlewareInterface|string $middleware
     *
     * @return RouteInterface
     * @noinspection PhpUnused
     */
    public function add($middleware): RouteInterface
    {
        return $this->route->add($middleware);
    }

    /**
     * @param MiddlewareInterface $middleware
     *
     * @return RouteInterface
     * @noinspection PhpUnused
     */
    public function addMiddleware($middleware): RouteInterface
    {
        return $this->route->add($middleware);
    }
}
