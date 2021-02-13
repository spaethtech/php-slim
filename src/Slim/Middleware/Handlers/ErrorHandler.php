<?php
declare(strict_types=1);

namespace MVQN\Slim\Middleware\Handlers;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;

/**
 * Class ErrorHandler
 *
 * @package MVQN\Slim\Error\Handlers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @copyright 2020 Spaeth Technologies, Inc.
 */
abstract class ErrorHandler
{
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

    /**
     * @param Response $response
     * @param string $path
     * @param array $data
     * @return Response
     *
     * @noinspection PhpIncludeInspection, PhpUnused, PhpUnusedParameterInspection
     */
    protected function render(Response $response, string $path, array $data = []): Response
    {
        ob_start();
        include($path);
        $template = ob_get_clean();

        $response->getBody()->write($template);

        return $response;
    }

}
