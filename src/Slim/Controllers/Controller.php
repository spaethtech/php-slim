<?php

namespace SpaethTech\Slim\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use SpaethTech\Support\Config\PhpConfig as Config;

/**
 * Controller
 *
 * @author    Ryan Spaeth <rspaeth@spaethtech.com>
 * @copyright 2022, Spaeth Technologies Inc.
 *
 */
abstract class Controller
{
    protected ContainerInterface $container;

    protected Config $config;

    public function __construct(ContainerInterface $container, Config $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    public static function validateQueryParam(string $query, string $pattern, mixed $default, array &$expected = [],  //int $pad = 0,
        callable $func = null) : bool
    {
        if(!preg_match($pattern, $query, $matches, PREG_UNMATCHED_AS_NULL))
            return false;

        foreach ($matches as $key => &$value)
        {
            if (is_int($key))
                unset($matches[$key]);

            if ($value == null)
                $value = $default;

            if ($func)
                $value = $func($value);
        }

        //if (count($matches) < $pad)
        //    $matches = array_pad($matches, $pad, $func($default));

        foreach($expected as $k => $v)
        {
            if (array_key_exists($k, $matches))
                $expected[$k] = $matches[$v];
        }


        //return $matches;
        $expected = $matches;

        return true;
    }


    public static function getCallingMethod()
    {
        return debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3)[2]["function"];

    }


}


