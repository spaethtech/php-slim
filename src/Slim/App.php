<?php
declare(strict_types=1);

namespace rspaeth\Slim;

use rspaeth\Slim\Middleware\Authentication\Authenticators\Authenticator;
use rspaeth\Slim\Middleware\Routing\QueryStringRouter;
use rspaeth\Slim\Controllers\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Interfaces\RouteResolverInterface;
use Slim\Psr7\Factory\ResponseFactory;

class App extends \Slim\App
{
    /**
     * @inheritDoc
     */
    public function __construct(
        ?ResponseFactoryInterface $responseFactory = null,
        ?ContainerInterface $container = null,
        ?CallableResolverInterface $callableResolver = null,
        ?RouteCollectorInterface $routeCollector = null,
        ?RouteResolverInterface $routeResolver = null,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null)
    {
        parent::__construct($responseFactory ?? new ResponseFactory(), $container, $callableResolver, $routeCollector, $routeResolver, $middlewareDispatcher);
    }

    /**
     * @param string $defaultRoute
     * @param array $rewriteRules
     * @param array $options
     * @return QueryStringRouter
     */
    public function addQueryStringRoutingMiddleware(
        string $defaultRoute = "/",
        array $rewriteRules = [],
        array $options = []): QueryStringRouter
    {
        $router = new QueryStringRouter($defaultRoute, $rewriteRules, $options);
        $this->add($router);
        return $router;
    }


    public function addAuthenticator(Authenticator $authenticator): Authenticator
    {
        $this->addMiddleware($authenticator);
        return $authenticator;
    }


    /**
     * @param Controller $controller
     *
     * @return RouteGroupInterface
     */
    public function addController(Controller $controller): RouteGroupInterface
    {
        return $controller($this);
    }

}
