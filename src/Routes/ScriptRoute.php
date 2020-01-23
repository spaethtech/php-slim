<?php
declare(strict_types=1);

namespace MVQN\Slim\Routes;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;
use Slim\App;
use Slim\Exception\HttpNotFoundException;

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
     */
    public function __construct(App $app, string $path)
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
                        //"route" => $request->getAttribute("vRoute"),
                        //"query" => $request->getAttribute("vQuery"),
                        //"user"  => $request->getAttribute("user"),
                        "attributes" => $request->getAttributes(),
                    ];

                    // NOTE: Inside any route closure, $this refers to the Application's Container.
                    /** @var Container $container */
                    $container = $this;

                    // Return the default 404 page!
                    //return $container->get("notFoundHandler")($request, $response, $data);
                    throw new HttpNotFoundException($request);
                }

                /** @noinspection PhpIncludeInspection */

                // Pass execution to the specified PHP file.
                include $path;

                // The PHP script should handle everything and since there is no Response to return, simply die()!
                die();
            }
        )->setName(ScriptRoute::class);

    }

}
