<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Middleware\Authentication\Authenticators;

//use Psr\Container\ContainerInterface as Container;
//use Psr\Http\Message\ServerRequestInterface as Request;
//use Psr\Http\Message\ResponseInterface as Response;

use Psr\Container\ContainerInterface as Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;


class CallbackAuthenticator extends Authenticator
{
    protected $authenticator;

    /**
     * @param callable|null $authenticator
     *
     * @throws Exceptions\InvalidCallbackException
     */
    public function __construct(callable $authenticator = null)
    {
        if ($authenticator !== null && !is_callable($authenticator))
            throw new Exceptions\InvalidCallbackException("The provided callback is non-callable!");

        $this->authenticator = $authenticator;
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
        $result = ($this->authenticator)($request, $response);

        $request = $request
            ->withAttribute("authenticator", get_class($this))
            ->withAttribute("authenticated", $result);

        return $next($request, $response);
    }
}
