<?php
declare(strict_types=1);

namespace MVQN\Slim\Middleware\Handlers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 * Class NotFoundHandler
 *
 * @package MVQN\Slim\Error\Handlers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
final class NotFoundHandler extends ErrorHandler
{
    /**
     * @param Request $request
     * @param Throwable $exception
     * @param bool $displayErrorDetails
     * @param bool $logErrors
     * @param bool $logErrorDetails
     *
     * @return Response
     */
    public function __invoke(Request $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): Response
    {
        // Setup some debugging information to pass along to the template...
        $data = [
            "debug"         => $displayErrorDetails,
            "vRoute"        => $request->getAttribute("vRoute"),
            "vQuery"        => $request->getAttribute("vQuery"),
            "authenticator" => $request->getAttribute("authenticator"),
            "routes"        => $this->app->getRouteCollector()->getRoutes(),
        ];

        $response = $this->app->getResponseFactory()->createResponse(404);
        //return $this->render($response, "404.html.twig", $data);
        return $this->render($response, __DIR__."/templates/404.php", $data);
    }

}
