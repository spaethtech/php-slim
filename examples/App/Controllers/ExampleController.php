<?php /** @noinspection PhpUnused, PhpUnusedParameterInspection */
declare(strict_types=1);

namespace App\Controllers;

use DateTime;
use MVQN\Slim\Application;
use MVQN\Slim\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteGroupInterface;

/**
 * An example Controller to handle routing and delivery using actions.
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
     * @param Application $app The {@see Application} to which this Controller belongs.
     *
     */
    public function __construct(Application $app)
    {
        parent::__construct($app, "/example");
    }

    /**
     * @inheritDoc
     * @noinspection PhpUnusedLocalVariableInspection
     */
    public function __invoke(Application $app): RouteGroupInterface
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

            // Add a route using a callable "action".
            $group->get("/date", [ $this, "date" ]);

        });

    }

    public function date(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write((new DateTime())->format("m/d/yy"));
        return $response;
    }

}
