<?php
declare(strict_types=1);

namespace MVQN\HTTP\Slim\Controllers;

use MVQN\HTTP\Slim\Middleware\CallbackAuthentication;
use Slim\App;
use Slim\Http\ServerRequest;
use Slim\Http\Response;


/**
 * Class AssetController
 *
 * Handles routing and provision of static assets.
 *
 * @package UCRM\Slim\Controllers\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class AssetController
{
    /**
     * AssetController constructor.
     *
     * @param App $app The Slim Application for which to configure routing.
     * @param string $path The path in which to find assets.
     * @param callable|null $authenticator
     */
    public function __construct(App $app, string $path, callable $authenticator = null)
    {
        // Get a local reference to the Slim Application's DI Container.
        $container = $app->getContainer();

        $app->get("/{file:.+}.{ext:jpg|png|pdf|txt|css|js|htm|html|svg|ttf|woff|woff2}",
            function (ServerRequest $request, Response $response, array $args) use ($container, $path)
            {
                // Get the file and extension from the matched route.
                $file = $args["file"];
                $ext = $args["ext"];

                // Interpolate the absolute path to the static asset.
                $path = rtrim($path, "/")."/$file.$ext";

                // IF the static asset file does not exist, THEN return a HTTP 404!
                if(!$path)
                    return $response->withStatus(404, "Asset '$file.$ext' not found!");

                // Specify the Content-Type given the extension...
                switch ($ext)
                {
                    case "jpg"   :                 $contentType = "image/jpg";                 break;
                    case "png"   :                 $contentType = "image/png";                 break;
                    case "pdf"   :                 $contentType = "application/pdf";           break;
                    case "txt"   :                 $contentType = "text/plain";                break;
                    case "css"   :                 $contentType = "text/css";                  break;
                    case "js"    :                 $contentType = "text/javascript";           break;
                    case "htm"   : case "html":    $contentType = "text/html";                 break;

                    case "svg"   :                 $contentType = "image/svg+xml";                  break;
                    case "ttf"   :                 $contentType = "application/x-font-ttf";         break;
                    case "otf"   :                 $contentType = "application/x-font-opentype";    break;
                    case "woff"  :                 $contentType = "application/font-woff";          break;
                    case "woff2" :                 $contentType = "application/font-woff2";         break;
                    case "eot"   :                 $contentType = "application/vnd.ms-fontobject";  break;
                    case "sfnt"  :                 $contentType = "application/font-sfnt";          break;

                    default      :                 $contentType = "application/octet-stream";  break;
                }

                // Set the response Content-Type header and write the contents of the file to the response body.
                $response = $response
                    ->withHeader("Content-Type", $contentType)
                    ->write(file_get_contents($path));

                // Then return the response!
                return $response;
            }
        )->add(new CallbackAuthentication($container, $authenticator))->setName("asset");
    }

}