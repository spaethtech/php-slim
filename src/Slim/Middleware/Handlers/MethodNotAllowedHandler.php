<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Middleware\Handlers;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;
use Slim\Router;
use Slim\Views\Twig;

final class MethodNotAllowedHandler
{

    public function __invoke(Container $container)
    {
        return function(Request $request, Response $response, array $methods) use ($container): Response
        {
            /** @var Router $router */
            $router = $container->get("router");

            /** @var Twig $twig */
            $twig = $container->get("twig");

            // Setup some debugging information to pass along to the template...
            $data = [
                "vRoute" => $request->getAttribute("vRoute"),
                "vQuery" => $request->getAttribute("vQuery"),
                "router" => $router,
                "methods" => $methods,
            ];

            // Load and parse our default template, using the above data.
            $template = $twig->fetchFromString(file_get_contents(__DIR__."/views/405.html.twig"), $data);

            // Set the appropriate headers and append the template to the response body.
            $response = $response->withStatus(405)->write($template);

            // Finally, return the response!
            return $response;
        };
    }

}
