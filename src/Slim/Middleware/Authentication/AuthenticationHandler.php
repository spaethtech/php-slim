<?php
declare(strict_types=1);

namespace rspaeth\Slim\Middleware\Authentication;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;
use Slim\Exception\HttpUnauthorizedException;

/**
 * Class AuthenticationHandler
 *
 * @package rspaeth\Slim\Middleware\Authentication
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @copyright 2020 Spaeth Technologies, Inc.
 */
class AuthenticationHandler implements MiddlewareInterface
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
     * @inheritDoc
     * @throws HttpUnauthorizedException
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        if($request->getAttribute("authenticated"))
        {
            return $handler->handle($request);
        }
        else
        {
            throw new HttpUnauthorizedException($request);
        }
    }
}
