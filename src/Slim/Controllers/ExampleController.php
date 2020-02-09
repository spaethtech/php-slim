<?php
declare(strict_types=1);

namespace MVQN\Slim\Controllers;

use DateTime;
use MVQN\Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteGroupInterface;

/**
 * A Controller to handle routing and delivery using actions.
 *
 * @package MVQN\Slim\Controllers
 * @final
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @copyright 2020 Spaeth Technologies, Inc.
 */
final class ExampleController extends Controller
{
    /**
     * AssetController constructor.
     *
     * @param App $app The Slim Application for which to configure routing.
     *
     */
    public function __construct(App $app)
    {
        parent::__construct($app, "/example");
    }

    /**
     * @inheritDoc
     */
    public function __invoke(App $app): RouteGroupInterface
    {
        return $this->group("", function(RouteCollectorProxyInterface $group)
        {
            $group->get("/test",
                function (Request $request, Response $response, array $args)
                {
                    $response->getBody()->write("TEST");
                    return $response;
                }
            )->setName(ExampleController::class);


            //$group->get("/date", [ $this, "date" ]);

        });

    }

    public function date(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write((new DateTime())->format("m/d/yy"));
        return $response;
    }


}
