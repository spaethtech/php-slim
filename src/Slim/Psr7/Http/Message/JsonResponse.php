<?php
declare(strict_types=1);

namespace rspaeth\Slim\Psr7\Http\Message;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

/**
 * Class JsonResponse
 *
 * @package rspaeth\Slim\Psr7\Http\Message
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @copyright 2020 Spaeth Technologies, Inc.
 */
class JsonResponse
{
    /**
     * The default JSON encoding options.
     */
    protected const DEFAULT_JSON_OPTIONS =
        JSON_UNESCAPED_UNICODE  |
        JSON_UNESCAPED_SLASHES  ;

    /**
     * @param array $data
     * @param int $options
     * @return ResponseInterface
     */
    public static function create(array $data, int $options = self::DEFAULT_JSON_OPTIONS): ResponseInterface
    {
        $response = (new ResponseFactory())->createResponse(200);
        return self::fromResponse($response, $data, $options);
    }

    /**
     * Constructs a {@see JsonResponse} from an existing {@see ResponseInterface}.
     *
     * @param ResponseInterface $response An existing {@see ResponseInterface} object.
     * @param array $data The data to be encoded as JSON.
     * @param int $options Optional encoding options, defaults to (JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).
     * @return ResponseInterface
     */
    public static function fromResponse(ResponseInterface $response, array $data, int $options = self::DEFAULT_JSON_OPTIONS): ResponseInterface
    {
        $response->getBody()->write(json_encode($data, $options));
        return $response->withHeader("Content-Type", "application/json");



        // Get the status, headers and body from the provided response, merging the necessary header(s) and body.
        //$status = $response->getStatusCode();
        //$headers = new Headers(array_merge($response->getHeaders(), ["Content-Type" => "application/json"]));
        //$body = $response->getBody();
        //$body->write(json_encode($data, $options));

        // Initialize this object using the new information.
        //parent::__construct($status, $headers, $body);
    }

}
