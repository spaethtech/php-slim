<?php
declare(strict_types=1);

namespace App\Controllers;

use DateTime;
use MVQN\Slim\App;
use MVQN\Slim\Controllers\Controller;
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
 * @author Ryan Spaeth
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
        // Mapped, in cases where a DI Container replaces the $this context in Closures.
        $self = $this;

        return $this->group("", function(RouteCollectorProxyInterface $group) use ($self)
        {
            $group->get("/test",
                function (Request $request, Response $response, array $args) use ($self)
                {
                    $response->getBody()->write("TEST");
                    return $response;
                }
            )->setName(ExampleController::class);


            //$group->get("/date", [ $this, "date" ]);

        });

    }

    public static function date(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write((new DateTime())->format("m/d/yy"));
        return $response;
    }


}
