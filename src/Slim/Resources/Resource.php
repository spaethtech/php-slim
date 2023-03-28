<?php
/** @noinspection PhpUnused */
declare(strict_types=1);

namespace SpaethTech\Slim\Resources;

use SpaethTech\Slim\Controllers\Controller;

/**
 * Class Resource
 *
 * @author Ryan Spaeth <rspaeth@spaethtech.com>
 * @copyright 2022 Spaeth Technologies Inc.
 */
abstract class Resource implements Cacheable
{
    protected Controller    $controller;
    protected string        $uri;
    protected ?array        $params     = null;
    protected ?string       $mime       = null;
    protected ?string       $contents   = null;
    protected ?string       $encoded    = null;

    #region Constructors

    /**
     * Constructor
     *
     * @param Controller $controller The Controller that handles this Resource.
     */
    public function __construct(Controller $controller, string $uri,
        ?array $params = null)
    {
        $this->controller = $controller;
        $this->uri = $uri;
        $this->params = $params;
    }

    #endregion

    #region Getters

    /**
     * @return Controller by which this Resource is being handled.
     */
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @return string URI for this Resource.
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return array|null All query parameters for this Resource request.
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * @return string A specific query parameter for this Resource request.
     */
    public function getParam(string $key): string
    {
        return array_key_exists($key, $this->params) ? $this->params[$key] : "";
    }

    /**
     * @param bool $update
     *
     * @return string
     */
    public function getMime(bool $update = FALSE): string
    {
        if ($this->mime === null || $update)
            $this->mime = mime_content_type($this->getLocation());

        return $this->mime;
    }

    /**
     * @param bool $update
     *
     * @return string
     */
    public function getContents(bool $update = FALSE): string
    {
        if ($this->contents === null || $update)
            $this->contents = file_get_contents($this->getLocation());

        return $this->contents;
    }

    /**
     * @param bool $update
     *
     * @return string
     */
    public function getEncoded(bool $update = FALSE): string
    {
        if ($this->encoded === null || $update)
            $this->encoded = base64_encode($this->getContents());

        return $this->encoded;
    }

    #endregion

    #region Methods

    /**
     * @return string The path to the assets' directory, per the Config.
     */
    public function getAssetsPath(): string
    {
        return $this->getController()->getConfig()->get("assets.path");
    }

    /**
     * @return string The path to the assets' cache directory, per the Config.
     */
    public function getAssetsCache(): string
    {
        return $this->getController()->getConfig()->get("assets.cache");
    }

    /**
     * @return string This Resource's directory.
     */
    public function getDirectory(): string
    {
        return $this->uri === "" ? "" : pathinfo($this->uri, PATHINFO_DIRNAME);
    }

    /**
     * @return string This Resource's filename.
     */
    public function getFilename(): string
    {
        return $this->uri === "" ? "" : pathinfo($this->uri, PATHINFO_FILENAME);
    }

    /**
     * @return string This Resource's extension.
     */
    public function getExtension(): string
    {
        return $this->uri === "" ? "" : pathinfo($this->uri, PATHINFO_EXTENSION);
    }

    /**
     * @param bool $real When TRUE, calls realpath() on the location.
     *
     * @return false|string The location of this Resource.
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getLocation(bool $real = FALSE)
    {
        if ($this->uri === "")
            return "";

        $dirs = [
            $this->getAssetsPath(),
            $this->getDirectory(),
            $this->getFilename(),
        ];

        $dirs = array_filter($dirs, function($dir) { return $dir !== ""; });
        $path = join("/", $dirs).".{$this->getExtension()}";

        return $real ? realpath($path) : $path;
    }

    /**
     * @param bool $real When TRUE, calls realpath() on the location.
     *
     * @return false|string The cached location of the Resource.
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getCachedLocation(bool $real = FALSE)
    {
        // IF no query parameters have been provided, THEN we want the original!
        if (is_null($this->params) || count($this->params) === 0)
            return $this->getLocation($real);

        if ($this->uri === "")
            return "";

        $dirs = [
            $this->getAssetsCache(),
            $this->getDirectory(),
            $this->getFilename(),
        ];

        $dirs   = array_filter($dirs, function($dir) { return $dir !== ""; });
        $suffix = $this->getCachedSuffix();
        $suffix = $suffix === "" ? "" : "--$suffix";
        $path   = join("/", $dirs)."$suffix.{$this->getExtension()}";

        return $real ? realpath($path) : $path;
    }

    /**
     * Determines if the Resource exists.
     *
     * @return bool TRUE if the resource exists, otherwise FALSE.
     */
    public function exists(): bool
    {
        $originalLocation = $this->getLocation(TRUE);
        return $originalLocation !== FALSE;
    }

    /**
     * Determines if the Resource is cached.
     *
     * @return bool TRUE if the resource is cached, otherwise FALSE.
     */
    public function cached(): bool
    {
        $cachedLocation = $this->getCachedLocation(TRUE);
        return $cachedLocation !== FALSE;
    }

    #endregion

    /**
     * Child classes should handle their own caching.
     *
     * @return bool TRUE on success, FALSE on failure.
     */
    public abstract function cache(): bool;

    /**
     * Child classes should handle their own file naming convention.
     *
     * @return string The suffix to include in the Resource's cached filename.
     */
    protected abstract function getCachedSuffix(): string;

}
