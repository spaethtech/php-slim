<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Controllers;

use MVQN\HTTP\Slim\Middleware\Authentication\AuthenticationHandler;
use MVQN\HTTP\Slim\Middleware\Authentication\Authenticators\Authenticator;

use Slim\App;
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
final class ScriptController
{
    /**
     * ScriptController constructor.
     *
     * @param App $app The Slim Application for which to configure routing.
     * @param string $path
     * @param Authenticator[]|Authenticator|null $authenticators
     */
    public function __construct(App $app, string $path, $authenticators = [])
    {
        $route = $app->map([ "GET", "POST" ], "/{file:.+}.{ext:php}",
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

                    // Return the default 404 page!
                    return $app->getContainer()->get("notFoundHandler")($request, $response, $data);
                }

                /** @noinspection PhpIncludeInspection */

                // Pass execution to the specified PHP file.
                include $path;

                // The PHP script should handle everything and since there is no Response to return, simply die()!
                die();
            }
        )->setName(ScriptController::class);

        if($authenticators !== null)
        {
            $route->add(new AuthenticationHandler($app->getContainer()));

            if(!is_array($authenticators))
                $authenticators = [ $authenticators ];

            foreach($authenticators as $authenticator)
            {
                if(is_a($authenticator, Authenticator::class))
                    $route->add($authenticator);
            }
        }
    }

}