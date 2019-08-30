<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Middleware;

use UCRM\HTTP\Twig\Extensions\PluginExtension;
//use Psr\Container\ContainerInterface as Container;
//use Psr\Http\Message\ServerRequestInterface as Request;
//use Psr\Http\Message\ResponseInterface as Response;

use Psr\Container\ContainerInterface as Container;
use Slim\Http\ServerRequest;
use Slim\Http\Response;


class CallbackAuthentication
{
    /**
     * @var Container A local reference to the Slim Framework DI Container.
     */
    protected $container;

    protected $authenticator;


    /**
     * PluginAuthentication constructor.
     *
     * @param Container $container
     * @param callable|null $authenticator
     */
    public function __construct(Container $container, callable $authenticator = null)
    {
        $this->container = $container;
        $this->authenticator = $authenticator;
    }


    /**
     * Middleware invokable class
     *
     * @param  ServerRequest $request The current PSR-7 Request object.
     * @param  Response $response The current PSR-7 Response object.
     * @param  callable $next The next middleware for which to pass control if this middleware does not fail.
     *
     * @return Response Returns a PSR-7 Response object.
     */
    public function __invoke(ServerRequest $request, Response $response, callable $next): Response
    {
        // Allow localhost!
        if($request->getUri()->getHost() === "localhost")
            return $next($request, $response);

        // IF a Session is not already started, THEN start one!
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        // Get the currently authenticated User, while also capturing the actual '/current-user' response!
        $user = Session::getCurrentUser();

        // Display an error if no user is authenticated!
        if(!$user)
            Log::http("No User is currently Authenticated!", Log::HTTP, 401);

        if($this->authenticator !== null && is_callable($this->authenticator) && !($this->authenticator)($user))
        {
            //Log::http("Currently Authenticated User is not allowed!", 401);
            http_response_code(401);
            exit();
        }

        // Set the current session user on the container, for later use in the application.
        $this->container["sessionUser"] = $user;
        PluginExtension::setGlobal("sessionUser", $user);
        $request = $request->withAttribute("sessionUser", $user);

        // If a valid user is authenticated and
        return $next($request, $response);
    }
}
