<?php /** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);



namespace MVQN\HTTP\Slim\Middleware\Routing;

define("TESTING", 123);

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use MVQN\HTTP\Slim\Routing\Exceptions\WebServerNotRunningException;
use PHPUnit\Framework\TestCase;



class QueryStringRouterTests extends TestCase
{


    private const HTTP_SERVER_HOST = "127.0.0.1";
    private const HTTP_SERVER_PORT = 80;

    /** @var Client|null The class-wide Guzzle HTTP Client. */
    private static $client = null;

    #region SET-UP / TEAR-DOWN

    public static function setUpBeforeClass(): void
    {
        $fp = @fsockopen(self::HTTP_SERVER_HOST, self::HTTP_SERVER_PORT, $code, $message, 1);

        if (!$fp)
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new WebServerNotRunningException(
                "No web server was found to be running on '".self::HTTP_SERVER_HOST.":".self::HTTP_SERVER_PORT."'!\n"
            );

        fclose($fp);

        self::$client = new Client([
            "base_uri"  => "http://".self::HTTP_SERVER_HOST.":".self::HTTP_SERVER_PORT."/",
            "timeout"   => 1.0,
        ]);
    }

    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    public static function tearDownAfterClass(): void
    {
        self::$client = null;
    }

    #endregion

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     *
     * @return Response
     * @throws GuzzleException
     */
    public function request(string $method = "GET", string $uri = "index.php", array $options = []): Response
    {

        return self::$client->request($method, $uri, $options);
    }

    public function testGetRoot(): void
    {
        $response = $this->request("GET", "index.php");
        $this->assertEquals(200, $response->getStatusCode());
        $body1 = $response->getBody()->getContents();

        $response = $this->request("GET", "index.php?");
        $this->assertEquals(200, $response->getStatusCode());
        $body2 = $response->getBody()->getContents();
        $this->assertEquals($body1, $body2);

        $response = $this->request("GET", "index.php?/");
        $this->assertEquals(200, $response->getStatusCode());
        $body3 = $response->getBody()->getContents();
        $this->assertEquals($body1, $body3);

        $response = $this->request("GET", "index.php?/index.html");
        $this->assertEquals(200, $response->getStatusCode());
        $body4 = $response->getBody()->getContents();
        $this->assertEquals($body1, $body4);
    }

    public function testGetExample(): void
    {
        $data = [
            "name" => "",
            "description" => "This is an example JSON route!"
        ];

        $response = $this->request("GET", "index.php?/example");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($data), $response->getBody()->getContents());

        $data = [
            "name" => "rspaeth",
            "description" => "This is an example JSON route!"
        ];

        $response = $this->request("GET", "index.php?/example/rspaeth");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($data), $response->getBody()->getContents());
    }

    public function testGetUnauthorized(): void
    {



    }



}