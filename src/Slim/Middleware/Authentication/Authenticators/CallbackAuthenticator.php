<?php
declare(strict_types=1);

namespace MVQN\Slim\Middleware\Authentication\Authenticators;

use Closure;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;


/**
 * Class CallbackAuthenticator
 *
 * @package MVQN\Slim\Middleware\Authentication\Authenticators
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class CallbackAuthenticator extends Authenticator
{
    /**
     * @var Closure
     */
    protected $authenticator;

    /**
     * @param Closure $authenticator
     */
    public function __construct(Closure $authenticator)
    {
        //if ($authenticator !== null && !is_callable($authenticator))
        //    throw new Exceptions\InvalidCallbackException("The provided callback is non-callable!");

        $this->authenticator = $authenticator;
    }

    /**
     * @inheritDoc
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $request = $request
            ->withAttribute("authenticator", get_class($this))
            ->withAttribute("authenticated", ($this->authenticator)($request));

        return $handler->handle($request);
    }
}
