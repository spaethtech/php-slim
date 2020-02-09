<?php /** @noinspection PhpUnusedParameterInspection */
declare(strict_types=1);

namespace MVQN\Slim\Controllers;

use MVQN\Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteGroupInterface;

/**
 * A Controller to handle routing and delivery of PHP scripts.
 *
 * @package MVQN\Slim\Controllers
 * @final
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @copyright 2020 Spaeth Technologies, Inc.
 */
final class ScriptController extends Controller
{
    /**
     * @var string The base path to use when loading scripts.
     */
    protected $path;

    /**
     * ScriptController constructor.
     *
     * @param App $app The Slim Application for which to configure routing.
     * @param string $path The base path to use when loading assets, defaults to "./scripts/".
     */
    public function __construct(App $app, string $path = "./scripts/")
    {
        parent::__construct($app);
        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(App $app): RouteGroupInterface
    {
        return $this->group("", function(RouteCollectorProxyInterface $group)
        {
            $group->map([ "GET", "POST" ], "/{file:.+}.{ext:php}",
                function (Request $request, Response $response, array $args)
                {
                    // Get the file and extension from the matched route.
                    $file = $args["file"] ?? "index";
                    $ext = $args["ext"] ?? "php";

                    // Interpolate the absolute path to the PHP script.
                    $path = rtrim($this->path, "/") . "/$file.$ext";

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
            )->setName(ScriptController::class);

        });
    }
}
