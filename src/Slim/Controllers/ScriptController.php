<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Controllers;

use MVQN\HTTP\Slim\Middleware\CallbackAuthentication;

use Slim\App;
use Slim\Http\ServerRequest;
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
final class ScriptController
{
    /**
     * ScriptController constructor.
     *
     * @param App $app The Slim Application for which to configure routing.
     * @param string $path
     * @param callable|null $authenticator
     */
    public function __construct(App $app, string $path, callable $authenticator = null)
    {
        // Get a local reference to the Slim Application's DI Container.
        $container = $app->getContainer();

        $app->map([ "GET", "POST" ], "/{file:.+}.{ext:php}",
            function (ServerRequest $request, Response $response, array $args) use ($container, $path)
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

                    // Return the default 404 page!
                    return $container->get("notFoundHandler")($request, $response, $data);
                }

                /** @noinspection PhpIncludeInspection */

                // Pass execution to the specified PHP file.
                include $path;

                // The PHP script should handle everything and since there is no Response to return, simply die()!
                die();
            }
        )->add(new CallbackAuthentication($container, $authenticator))->setName("script");
    }

}