<?php
declare(strict_types=1);

namespace MVQN\Slim\Middleware\Authentication;

use MVQN\Slim\Middleware\Handlers\UnauthorizedHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Stream;


class AuthenticationHandler
{
    /**
     * @var App
     */
    protected $app;

    /**
     * AuthenticationHandler constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     * @throws HttpUnauthorizedException
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if($request->getAttribute("authenticated"))
        {
            return $handler->handle($request);
        }
        else
        {
            //$response = $this->app->getResponseFactory()->createResponse(401, "Unauthorized");
            //$response = (new UnauthorizedHandler($this->app))($request, $response);
            //return $response;
            throw new HttpUnauthorizedException($request);
        }
    }

}
