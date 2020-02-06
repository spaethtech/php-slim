<?php /** @noinspection PhpUnusedParameterInspection */
declare(strict_types=1);

namespace MVQN\Slim\Routes;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use Slim\Exception\HttpNotFoundException;

/**
 * Class ScriptRoute
 *
 * Handles routing and response of PHP scripts.
 *
 * @package MVQN\Slim\Routes
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class ScriptRoute extends BuiltInRoute
{
    public const METHODS = [ "GET", "POST" ];
    public const PATTERN = "/{file:.+}.{ext:php}";

    /**
     * ScriptController constructor.
     *
     * @param App $app The Slim Application for which to configure routing.
     * @param string $path
     */
    public function __construct(App $app, string $path)
    {
        $this->route = $app->map(self::METHODS, self::PATTERN,
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
                    // Return the default 404 page!
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
