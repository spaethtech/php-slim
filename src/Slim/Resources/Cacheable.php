<?php
/** @noinspection PhpUnused */
declare(strict_types=1);

namespace SpaethTech\Slim\Resources;

/**
 * Interface Cacheable
 *
 * @author Ryan Spaeth <rspaeth@spaethtech.com>
 * @copyright 2022 Spaeth Technologies Inc.
 */
interface Cacheable
{
    /**
     * Handles caching of the Resource to the filesystem.
     *
     * @return bool TRUE on success, FALSE on failure.
     */
    function cache(): bool;
}
