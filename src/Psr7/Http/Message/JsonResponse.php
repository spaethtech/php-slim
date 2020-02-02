<?php
declare(strict_types=1);

namespace MVQN\Slim\Psr7\Http\Message;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

class JsonResponse extends Response
{

    protected const DEFAULT_JSON_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;

    /**
     * Converts the provided {@see ResponseInterface} into a JSON response.
     *
     * @param ResponseInterface $response The current response object.
     * @param array $data The data to be parsed as JSON.
     * @param int $options Any JSON encoding options.
     * @return ResponseInterface Returns the altered response.
     */
    public static function fromResponse(ResponseInterface $response, array $data,
        int $options = self::DEFAULT_JSON_OPTIONS): ResponseInterface
    {
        $response->getBody()->write(json_encode($data, $options));
        return $response->withHeader("Content-Type", "application/json");
    }


}
