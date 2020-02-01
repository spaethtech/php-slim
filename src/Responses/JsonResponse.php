<?php
declare(strict_types=1);


namespace MVQN\Slim\Responses;



use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Psr7\Interfaces\HeadersInterface;
use Slim\Psr7\Response;

class JsonResponse extends Response
{

    protected const JSON_DEFAULT_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;

    public function __construct(array $data, int $status = StatusCodeInterface::STATUS_OK, ?HeadersInterface $headers = null, ?StreamInterface $body = null)
    {
        parent::__construct($status, $headers, $body);

        $this->getBody()->write(json_encode($data, self::JSON_DEFAULT_OPTIONS));
        $this->withHeader("Content-Type", "application/json");
    }

    public static function fromResponse(ResponseInterface $response, array $data, int $options = self::JSON_DEFAULT_OPTIONS): ResponseInterface
    {
        $response->getBody()->write(json_encode($data, $options));
        return $response->withHeader("Content-Type", "application/json");
    }


}
