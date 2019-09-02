<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Middleware\Routing;

use MVQN\HTTP\Twig\Extensions\QueryStringRoutingExtension;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Twig\Loader\FilesystemLoader;

/**
 * Class QueryStringRouter
 *
 * @package UCRM\Routing\Middleware
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 */
class QueryStringRouter
{
    // =================================================================================================================
    // CONSTANTS
    // =================================================================================================================

    /**
     * @var array Supported file extensions that can be used for automatic lookup.  Prioritized by the provided order!
     * @deprecated
     */
    protected const AUTO_EXTENSIONS = [ "php", "html", "twig", "html.twig", "jpg", "png", "pdf", "txt", "css", "js" ];

    // =================================================================================================================
    // PROPERTIES
    // =================================================================================================================

    /**
     * @var array A collection of paths to search for files when routing.
     * @deprecated
     */
    protected $paths;

    // =================================================================================================================
    // AUTO EXTENSIONS
    // =================================================================================================================

    /**
     * Attempts to determine the correct file extension, when none is provided in the path.
     *
     * @param string $path The path for which to inspect.
     * @param array $extensions An optional array of supported extensions, ordered for detection priority.
     * @return string|null Returns an automatic path, if the file exists OR NULL if no determination could be made!
     * @deprecated
     */
    protected function autoExtension(string $path, array $extensions = self::AUTO_EXTENSIONS): ?string
    {
        // IF a valid path with extension was provided...
        if(realpath($path))
        {
            // THEN determine the extension part and return it!
            $parts = explode(".", $path);
            $ext = $parts[count($parts) - 1];
            return $ext;
        }
        else
        {
            // OTHERWISE, assume no extension was provided and try suffixing from the list of auto extensions...
            foreach ($extensions as $extension)
            {
                // IF the current path with auto extension exists, THEN return the extension!
                if (realpath($path . ".$extension") && !is_dir($path . ".$extension"))
                    return $extension;
            }

            // If all else fails, return NULL!
            return null;
        }
    }






    /** @var string The default route. */
    protected $defaultRoute;

    /** @var array Any optional rewrite rules. */
    protected $rewriteRules;

    /** @var array Any middleware options. */
    protected $options;

    /**
     * QueryStringRouter constructor.
     *
     * @param string $defaultRoute The default route to use when no match is found in the query string.
     * @param array $rewriteRules Any optional rewrite rules to use on the found route, applied in order.
     * @param array $options Any options to pass to this middleware.
     */
    public function __construct(string $defaultRoute = "/", array $rewriteRules = [], array $options = [])
    {
        $this->defaultRoute = $defaultRoute;
        $this->rewriteRules = $rewriteRules;
        $this->options = $options;

        QueryStringRoutingExtension::addGlobal("defaultRoute", $defaultRoute);
    }

    /**
     * QueryStringRouter middleware as invocable.
     *
     * @param  ServerRequestInterface $request The current PSR-7 ServerRequest object.
     * @param  ResponseInterface $response The current PSR-7 Response object.
     * @param  callable $next The Next middleware.
     *
     * @return ResponseInterface Returns a PSR-7 Response object.
     */
    public function __invoke($request, $response, $next)
    {
        // Get the current query if set, otherwise set it as the default route only.
        $queryString = $_SERVER["QUERY_STRING"] ?? $this->defaultRoute;

        // Extract (and remove) the route from the query string.
        $vRoute = $this->extractRouteFromQueryString($queryString, $this->rewriteRules);

        // Parse the
        parse_str($queryString, $vQuery);

        $uri = $request->getUri()
            ->withPath($vRoute)
            ->withQuery($queryString);

        $request = $request
            ->withUri($uri)
            ->withQueryParams($vQuery)
            ->withAttribute("vRoute", $vRoute)
            ->withAttribute("vQuery", $vQuery);


        $_GET = $vQuery;
        $_SERVER["QUERY_STRING"] = $queryString;

        $response = $next($request, $response);

        return $response;
    }

    public static function extractRouteFromQueryString(string &$queryString, array $rewriteRules = []): string
    {
        // NOTE: We use our our parameter parsing here, to make sure things are handled OUR way!

        // Convert any URL encodings back to slashes.
        $queryString = str_replace("%2F", "/", $queryString);

        // Split the query parameters.
        $parts = explode("&", $queryString);

        // Set some initialized values.
        $route = "";
        $query = [];

        // NOTE: IF multiple route parameters are found, the last one takes precedence!

        // loop through each parameter...
        foreach($parts as $part)
        {
            // IF the parameter starts with "/", THEN assume it's a route.
            if      (strpos($part, "/") === 0)          $route = $part;
            // IF the parameter starts with "route=/" OR "r=/", THEN assume it's a route.
            else if (strpos($part, "route=/") === 0)    $route = str_replace("route=/", "/", $part);
            else if (strpos($part, "r=/") === 0)        $route = str_replace("r=/", "/", $part);
            // OTHERWISE, assume it's a normal query parameter.
            else                                        $query[] = $part;
        }

        foreach($rewriteRules as $pattern => $replacement)
            $route = preg_replace($pattern, $replacement, $route);

        $queryString = implode("&", $query);

        return $route;
    }

}