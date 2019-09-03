<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Middleware\Views;

use Interop\Container\Exception\ContainerException;
use MVQN\HTTP\Slim\Middleware\Views\Exceptions\InvalidTemplatePathException;
use MVQN\HTTP\Twig\Extensions\QueryStringRoutingExtension;
use MVQN\HTTP\Twig\Extensions\SwitchExtension;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;
use Slim\Router;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;

final class TwigView
{
    /** @var string The default auto-detect template folder. */
    private const DEFAULT_AUTO_DETECT_FOLDER = "views";

    /** @var array The default Twig Environment options. */
    private const DEFAULT_ENVIRONMENT_OPTIONS = [ "debug" => true ];


    private $paths = [];
    private $options = [];
    private $globals = [];

    /**
     * TwigView constructor.
     *
     * @param string[]|string|null $paths The path(s) in which to find the Twig templates.
     * @param array $options Any options to be used when creating the Twig Environment.
     * @param array $globals Any optional global values to be used via "app.<name>" in the Twig templates.
     *
     * @throws InvalidTemplatePathException
     */
    public function __construct($paths = null, array $options = self::DEFAULT_ENVIRONMENT_OPTIONS, array $globals = [])
    {
        #region Path Validation

        // IF a path or paths were provided...
        if($paths !== null && $paths !== "")
        {
            // ...THEN proceed to validate the paths.

            // Coerce the provided "paths" argument into an array, when a string was provided.
            $paths = is_array($paths) ? $paths : [ $paths ];

            // Loop through each provided path...
            foreach($paths as $path)
            {
                // Attempt to determine the absolute path from the current path.
                $real = realpath($path);

                // IF the path does not exist OR is not a directory, THEN throw an Exception!
                if(!$real || !is_dir($real))
                    continue;
                /*
                throw new InvalidTemplatePathException(
                    "\n".
                    "Invalid Twig template path provided:\n".
                    "    '$path'\n".
                    "    Make sure the path exists and is a directory!\n");
                */

                // OTHERWISE, add it to the list of template paths.
                $this->paths[] = $real;
            }
        }

        #endregion

        #region Path Auto-Detection

        // IF no paths were provided OR all of the provided paths were invalid...
        if($paths === null || $paths === "" || count($this->paths) === 0)
        {
            // ...THEN attempt to auto-detect a valid template path.

            // Get the calling script from the backtrace and use the containing path as the "auto-detect" root.
            $trace = debug_backtrace();
            $file = $trace[0]["file"];
            $root = dirname($file);

            // Attempt to determine the absolute path from the "auto-detect" root and default views folder.
            $path = $root. DIRECTORY_SEPARATOR . self::DEFAULT_AUTO_DETECT_FOLDER;
            $real = realpath($path);

            // IF the path does not exist, THEN throw an Exception!
            if(!$real)
                //throw new InvalidTemplatePathException(
                //    "\n".
                //    "No valid template paths were provided and auto-detection of path '$path' was also invalid!\n");
                mkdir($path, 0775, true);

            // OTHERWISE, add the auto-detected path!
            $this->paths = [ $real ];
        }

        #endregion

        // Include any provided options and globals.
        $this->options = $options;
        $this->globals = $globals;
    }

    /**
     * @param Container $container The Slim Framework DI Container.
     *
     * @return Twig Returns a Twig instance.
     * @throws ContainerException
     */
    public function __invoke(Container $container)
    {
        // Create a new instance of the Twig template renderer and configure the default options...
        $twig = new Twig($this->paths, $this->options);

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
        $twig->addExtension(new QueryStringRoutingExtension(
            [ "debug" => $twig->getEnvironment()->isDebug() ] + $this->globals
        ));

        // Finally, return the newly configured Slim/Twig Renderer.
        return $twig;
    }

}
