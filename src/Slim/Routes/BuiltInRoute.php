<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Routes;

use Slim\App;
use Slim\Interfaces\RouteInterface;

abstract class BuiltInRoute
{
    /** @var RouteInterface */
    protected $route;

    /**
     * @param $callable|string
     *
     * @return RouteInterface
     */
    public function add($callable)
    {
        //if(is_a($callable, Authenticator::class) && !$this->hasAuthenticator)
        //$this->route->add(new AuthenticationHandler($app->getContainer()));

        return $this->route->add($callable);
    }

}
