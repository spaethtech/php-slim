<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Middleware\Authentication;

//use Psr\Container\ContainerInterface as Container;
//use Psr\Http\Message\ServerRequestInterface as Request;
//use Psr\Http\Message\ResponseInterface as Response;

use Psr\Container\ContainerInterface as Container;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;


class AuthenticationHandler
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Middleware invokable class
     *
     * @param  Request $request The current PSR-7 Request object.
     * @param  Response $response The current PSR-7 Response object.
     * @param  callable $next The next middleware for which to pass control if this middleware does not fail.
     *
     * @return Response Returns a PSR-7 Response object.
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $authenticated = $request->getAttribute("authenticated");

        if($authenticated)
            return $next($request, $response);
        else
            return $this->container->get("unauthorizedHandler")($request, $response);
    }
}
