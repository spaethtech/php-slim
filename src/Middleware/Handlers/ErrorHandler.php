<?php
declare(strict_types=1);

namespace MVQN\Slim\Middleware\Handlers;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
 * Class ErrorHandler
 *
 * @package MVQN\Slim\Error\Handlers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
abstract class ErrorHandler
{
    /**
     * @var Environment
     */
    protected static $twig;

    /**
     * @var App
     */
    protected $app;

    /**
     * ErrorHandler constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    protected function render(Response $response, string $template, array $data = []): Response
    {
        if (!self::$twig)
        {
            self::$twig = new Environment(
                new FilesystemLoader(realpath(__DIR__ . "/templates/")),
                [ "cache" => realpath(__DIR__ . "/templates/.cache/") ]
            );
        }

        try
        {
            $response->getBody()->write(
                self::$twig->render($template, array_merge($data, [ "script" => $_SERVER['SCRIPT_NAME'] ]))
            );
        }
        catch (LoaderError $e)
        {
            //$response = $response->withStatus(500);
            //$response->getBody()->write("Failed to load Twig templates from file system.");
        }
        catch (RuntimeError $e)
        {
            //$response = $response->withStatus(500);
            //$response->getBody()->write("Failed to load Twig templates from file system.");
        }
        catch (SyntaxError $e)
        {
            //$response = $response->withStatus(500);
            //$response->getBody()->write("Failed to load Twig templates from file system.");
        }

        return $response;
    }

}