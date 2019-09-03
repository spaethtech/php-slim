<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Routes;

use MVQN\HTTP\Slim\Middleware\Authentication\AuthenticationHandler;
use MVQN\HTTP\Slim\Middleware\Authentication\Authenticators\Authenticator;

use Slim\App;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class TemplateController
 *
 * Handles routing and subsequent rendering of Twig templates.
 *
 * @package UCRM\Slim\Controllers\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class TemplateRoute extends BuiltInRoute
{
    /**
     * TemplateController constructor.
     *
     * @param App $app The Slim Application for which to configure routing.
     * @param string $path
     * @param Authenticator[]|Authenticator|null $authenticators
     */
    public function __construct(App $app, string $path)//, $authenticators = [])
    {
        $this->route = $app->get("/{file:.+}.{ext:twig}",
            function (Request $request, Response $response, array $args) use ($app, $path)
            {
                // Get the file and extension from the matched route.
                $file = $args["file"] ?? "index";
                $ext = $args["ext"] ?? "html";

                // Interpolate the absolute path to the static HTML file or Twig template.
                $templates = rtrim($path, "/") . "/$file.$ext";

                // Get a local reference to the Twig template renderer.
                $twig = $app->getContainer()->get("twig");

                // Assemble some standard data to send along to the Twig template!
                $data = [
                    "route" => $request->getAttribute("vRoute"),
                    "query" => $request->getAttribute("vQuery"),
                    "user"  => $request->getAttribute("user"),
                ];

                // IF the file exists exactly as specified...
                if (file_exists($templates) && !is_dir($templates))
                    // THEN render the file.
                    return $twig->render($response, "$file.$ext", $data);
                else
                {
                    // NOTE: Inside any route closure, $this refers to the Application's Container.
                    /** @var Container $container */
                    $container = $this;

                    // OTHERWISE, return the default 404 page!
                    return $container->get("notFoundHandler")($request, $response, $data);
                }
            }
        )->setName(TemplateRoute::class);

        /*
        if($authenticators !== null)
        {
            // NOTE: However, outside the route closure, $this refers to the current object like usual!
            $route->add(new AuthenticationHandler($app->getContainer()));

            if(!is_array($authenticators))
                $authenticators = [ $authenticators ];

            foreach($authenticators as $authenticator)
            {
                if(is_a($authenticator, Authenticator::class))
                    $route->add($authenticator);
            }
        }
        */
    }


}