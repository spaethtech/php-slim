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
 * Class ScriptController
 *
 * Handles routing of PHP scripts.
 *
 * @package UCRM\Slim\Controllers\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class ScriptRoute extends BuiltInRoute
{
    /**
     * ScriptController constructor.
     *
     * @param App $app The Slim Application for which to configure routing.
     * @param string $path
     * @param Authenticator[]|Authenticator|null $authenticators
     */
    public function __construct(App $app, string $path)//, $authenticators = [])
    {
        $this->route = $app->map([ "GET", "POST" ], "/{file:.+}.{ext:php}",
            function (Request $request, Response $response, array $args) use ($app, $path)
            {
                // Get the file and extension from the matched route.
                $file = $args["file"] ?? "index";
                $ext = $args["ext"] ?? "php";

                // Interpolate the absolute path to the PHP script.
                $path = rtrim($path, "/") . "/$file.$ext";

                // IF the PHP script file does not exist, THEN return a 404 page!
                if(!file_exists($path))
                {
                    // Assemble some standard data to send along to the 404 page for debugging!
                    $data = [
                        "route" => $request->getAttribute("vRoute"),
                        "query" => $request->getAttribute("vQuery"),
                        "user"  => $request->getAttribute("user"),
                    ];

                    // NOTE: Inside any route closure, $this refers to the Application's Container.
                    /** @var Container $container */
                    $container = $this;

                    // Return the default 404 page!
                    return $container->get("notFoundHandler")($request, $response, $data);
                }

                /** @noinspection PhpIncludeInspection */

                // Pass execution to the specified PHP file.
                include $path;

                // The PHP script should handle everything and since there is no Response to return, simply die()!
                die();
            }
        )->setName(ScriptRoute::class);

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