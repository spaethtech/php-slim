<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Middleware\Views;

use MVQN\HTTP\Twig\Extensions\QueryStringRoutingExtension;
use MVQN\HTTP\Twig\Extensions\SwitchExtension;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;
use Slim\Router;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;

final class TwigView
{
    protected $paths = [];
    protected $options = [];

    /**
     * TwigView constructor.
     *
     * @param string[]|string $paths
     * @param $options
     */
    public function __construct($paths, array $options = [ "debug" => true ])
    {
        $this->paths = is_array($paths) ? $paths : [ $paths ];
        $this->options = $options;
    }

    public function __invoke(Container $container)
    {
        // Create a new instance of the Twig template renderer and configure the default options...
        $twig = new \Slim\Views\Twig($this->paths, $this->options);

        // Get the current Slim Router and initialize some defaults from the Environment.
        /** @var Router $router */
        $router = $container->get("router");
        $uri = Uri::createFromEnvironment(new Environment($_SERVER));

        // Now add the standard TwigExtension to the specialized Slim/Twig view system.
        $twig->addExtension(new TwigExtension($router, $uri));

        // TODO: Determine if there is a replacement to this deprecated extension!
        $twig->addExtension(new DebugExtension());

        // Add our custom SwitchExtension for using {% switch/case/default %} tokens in the Twig templates.
        $twig->addExtension(new SwitchExtension());

        // Add our custom PluginExtension for using some Plugin-specific globals, functions and filters.
        $twig->addExtension(new QueryStringRoutingExtension([
            "debug" => $twig->getEnvironment()->isDebug(),
        ]));

        // Finally, return the newly configured Slim/Twig Renderer.
        return $twig;
    }

}