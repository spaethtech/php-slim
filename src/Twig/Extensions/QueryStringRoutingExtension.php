<?php
declare(strict_types=1);
namespace MVQN\HTTP\Twig\Extensions;

use MVQN\Common\Arrays;
use MVQN\Common\Strings;
use Slim\App;
use Slim\Container;
use Slim\Router;

use Twig\Extension\GlobalsInterface;
use Twig\Extension;
use Twig\TwigFilter;
use Twig\TwigFunction;


/**
 * Class Extension
 *
 * @package MVQN\Twig
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class QueryStringRoutingExtension extends Extension\AbstractExtension implements GlobalsInterface
{
    /** @var array */
    protected static $globals = [
        "app" => [
            // NOTE: Add any desired defaults here...
            "test3" => "ABC",
        ]
    ];



    public function __construct(array $globals = [])
    {
        foreach($globals as $key => $value)
            self::$globals["app"][$key] = $value;

        // NOTE: Add any other global defaults here...
        //self::$globals["app"]["test"] = false;

        //self::$globals = $globals;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return "QueryStringRouting";
    }

    /**
     * @return array
     */
    public function getTokenParsers(): array
    {
        return [];
    }

    #region FILTERS

    /**
     * @return array
     */
    public function getFilters(): array
    {

        return [
            new TwigFilter("uncached", [$this, "uncached"]),
        ];
    }

    /**
     * @param string $path
     *
     * @return string
     * @throws \Exception
     */
    public function uncached(string $path)
    {
        $uncachedPath = "";

        if(Strings::contains($path, "?"))
        {
            $parts = explode("?", $path);

            parse_str($parts[1], $query);

            var_dump($query);

            $query["v"] = (new \DateTime())->getTimestamp();
            $queryParts = [];

            foreach($query as $key => $value)
                $queryParts[] = "$key=$value";

            $uncachedPath = $parts[0]."?".implode("&", $queryParts);
        }
        else
        {
            $uncachedPath = $path."?v=".(new \DateTime())->getTimestamp();
        }

        return $uncachedPath;
    }

    #endregion

    #region FUNCTIONS

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction("link", [$this, "link"]),

        ];
    }

    /**
     * @param string $path
     * @param bool $relative
     * @return string
     * @throws \Exception
     */
    public function link(string $path, bool $relative = true): string
    {
        $fragment = "";
        if(strpos($path, "#") !== false)
        {
            $fragment = substr($path, strpos($path, "#"));
            $path = str_replace($fragment, "", $path);
        }

        // Split the provided path into
        list($path, $query) = $path !== "" ? explode("?", strpos("?", $path) !== false ? $path : "$path?") : ["", ""];

        //var_dump($path, $query);

        $baseUrl = self::$globals["app"]["baseUrl"] ?? "";
        $baseScript = self::$globals["app"]["baseScript"] ?? "";

        $path = ($path === "/" && $baseScript !== "") || $path === "" ? "" : ($baseScript !== "" ? "?" : "")."$path";

        $link = $relative ? $baseScript.$path :  $baseUrl.$baseScript.$path;
        $link .= $query !== "" ? ($baseScript !== "" && $path !== "" ? "&" : "?")."$query" : "";

        return $link.$fragment ?: $path;
    }

    #endregion






    public function getGlobals(): array
    {
        return self::$globals;
    }

    public static function addGlobal(string $name, $value)
    {
        self::$globals["app"][$name] = $value;
    }

}
