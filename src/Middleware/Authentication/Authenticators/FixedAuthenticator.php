<?php
declare(strict_types=1);

namespace MVQN\Slim\Middleware\Authentication\Authenticators;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;


class FixedAuthenticator extends Authenticator
{

    /**
     * @var bool
     */
    protected $fixed;

    /**
     * @param bool $fixed
     */
    public function __construct(bool $fixed)
    {
        $this->fixed = $fixed;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $request = $request
            ->withAttribute("authenticator", get_class($this))
            ->withAttribute("authenticated", $this->fixed);

        return $handler->handle($request);
    }



}
