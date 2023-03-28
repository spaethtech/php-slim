<?php
declare(strict_types=1);

namespace SpaethTech\Slim\Controllers;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use SpaethTech\Slim\Resources\Resource;
use SpaethTech\Support\Config\AbstractConfig as Config;

/**
 * Class Controller
 *
 * @author Ryan Spaeth <rspaeth@spaethtech.com>
 * @copyright 2022 Spaeth Technologies Inc.
 */
abstract class Controller
{
    protected Container $container;
    protected Config    $config;
    protected Resource  $resource;

    #region Constructors

    /**
     * Constructor
     *
     * @param Container $container The Application's DI Container.
     * @param Config $config                The Application's Config.
     */
    public function __construct(Container $container, Config $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    #endregion

    #region Getters

    /**
     * @return Container The Application's DI Container.
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @return Config The Application's Config.
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Child classes should handle their own Resource creation.
     *
     * @param Request $request This Controller's current Request.
     *
     * @return Resource The Controller's Response.
     */
    protected abstract function getResource(Request $request): Resource;

    #endregion

    #region Methods

    /**
     * Child classes should handle their own Resource rendering.
     *
     * @param Request $request This Controller's current Request.
     * @param Response $response This Controller's current Response.
     *
     * @return Response The Controller's Response.
     */
    public abstract function render(Request $request, Response $response): Response;

    /**
     * The Controller's default Request handler.
     *
     * @param Request $request This Controller's current Request.
     * @param Response $response This Controller's current Response.
     *
     * @return Response The Controller's Response.
     *
     * @throws HttpNotFoundException
     */
    public function __invoke(Request $request, Response $response): Response
    {
        // Create a new Resource.
        $this->resource = $this->getResource($request);

        // IF the Resource does not exist, THEN return a 404!
        if(!$this->resource->exists())
            throw new HttpNotFoundException($request);

        // Render the Resource.
        return $this->render($request, $response);
    }

    #endregion

}


