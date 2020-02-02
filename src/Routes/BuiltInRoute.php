<?php
declare(strict_types=1);

namespace MVQN\Slim\Routes;

use Psr\Http\Server\MiddlewareInterface;
use Slim\Interfaces\RouteInterface;

/**
 * Class BuiltInRoute
 *
 * @package MVQN\Slim\Routes
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
abstract class BuiltInRoute
{
    /** @var RouteInterface */
    protected $route;

    /**
     * @param callable|MiddlewareInterface|string $callable
     *
     * @return RouteInterface
     */
    public function add($callable)
    {
        return $this->route->add($callable);
    }

}
