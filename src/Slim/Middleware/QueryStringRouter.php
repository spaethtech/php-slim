<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Middleware;

use Psr\Container\ContainerInterface;
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
     */
    protected const AUTO_EXTENSIONS = [ "php", "html", "twig", "html.twig", "jpg", "png", "pdf", "txt", "css", "js" ];

    // =================================================================================================================
    // PROPERTIES
    // =================================================================================================================

    /** @var array A collection of paths to search for files when routing. */
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


    public static function extractRouteFromQueryString(string &$queryString, string $defaultRoute = "/index.php"): string
    {
        //echo "$queryString\n";
        $queryString = str_replace("%2F", "/", $queryString);

        $parts = explode("&", $queryString);

        $route = "";
        $query = [];

        foreach($parts as $part)
        {
            if      (strpos($part, "/") === 0)          $route = $part;
            else if (strpos($part, "route=/") === 0)    $route = str_replace("route=/", "/", $part);
            else if (strpos($part, "r=/") === 0)        $route = str_replace("r=/", "/", $part);
            else                                        $query[] = $part;
        }

        if ($route === "/")
            $route = $defaultRoute; // "/index.php";

        $queryString = implode("&", $query);

        return $route;
    }

    private $defaultRoute;

    public function __construct(string $defaultRoute = "/index.php")
    {
        $this->defaultRoute = $defaultRoute;
    }


    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $queryString = isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] !== "" ? $_SERVER["QUERY_STRING"] : $this->defaultRoute;   // "/";

        /*
        echo "<pre>";
        echo var_dump($_SERVER);
        echo "</pre>";
        */

        $route = $this->extractRouteFromQueryString($queryString, $this->defaultRoute);

        parse_str($queryString, $query);

        $uri = $request->getUri()
            ->withPath($route)
            ->withQuery($queryString);

        $request = $request
            ->withUri($uri)
            ->withQueryParams($query)
            ->withAttribute("vRoute", $route)
            ->withAttribute("vQuery", $query);

        /*
        echo "<pre>";
        echo var_dump($request);
        echo "</pre>";
        */

        $_GET = $query;
        $_SERVER["QUERY_STRING"] = $queryString;

        $response = $next($request, $response);

        return $response;
    }
}