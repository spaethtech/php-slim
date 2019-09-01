<?php
declare(strict_types=1);
namespace MVQN\HTTP\Twig\Extensions;

use MVQN\HTTP\Twig\TokenParsers\SwitchTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Class SwitchExtension
 *
 * @package MVQN\Twig\Extensions
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class SwitchExtension extends AbstractExtension implements GlobalsInterface
{
    public function getName(): string
    {
        return "switch";
    }

    public function getTokenParsers(): array
    {
        return [
            new SwitchTokenParser(),
        ];
    }

    public function getFilters(): array
    {

        return [
            //new \Twig_SimpleFilter('without', [$this, 'withoutFilter']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            //new \Twig_SimpleFunction('getFootHtml', [$this, 'getFootHtml']),
        ];
    }

    public function getGlobals(): array
    {
        return [
            //'now' => new \DateTime(),
        ];
    }


}