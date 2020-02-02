<?php
declare(strict_types=1);

namespace MVQN\Slim\Routes;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\App;
use Slim\Exception\HttpNotFoundException;

/**
 * Class AssetController
 *
 * Handles routing and response of static assets.
 *
 * @package MVQN\Slim\Routes
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class AssetRoute extends BuiltInRoute
{
    /**
     * AssetController constructor.
     *
     * @param App $app The Slim Application for which to configure routing.
     * @param string $path
     *
     * @noinspection SpellCheckingInspection
     */
    public function __construct(App $app, string $path)
    {
        $this->route = $app->get("/{file:.+}.{ext:jpg|png|pdf|txt|css|js|htm|html|svg|ttf|woff|woff2}",
            function (Request $request, Response $response, array $args) use ($app, $path)
            {
                // Get the file and extension from the matched route.
                $file = $args["file"];
                $ext = $args["ext"];

                // Interpolate the absolute path to the static asset.
                $path = realpath(rtrim($path, "/") . "/$file.$ext");

                // IF the static asset file does not exist, THEN return a HTTP 404!
                if(!$path)
                {
                    // Return the default 404 page!
                    throw new HttpNotFoundException($request);
                }

                // Specify the Content-Type given the extension...
                switch ($ext)
                {
                    case "jpg"   :                 $contentType = "image/jpg";                      break;
                    case "png"   :                 $contentType = "image/png";                      break;
                    case "pdf"   :                 $contentType = "application/pdf";                break;
                    case "txt"   :                 $contentType = "text/plain";                     break;
                    case "css"   :                 $contentType = "text/css";                       break;
                    case "js"    :                 $contentType = "text/javascript";                break;
                    case "htm"   : case "html":    $contentType = "text/html";                      break;

                    case "svg"   :                 $contentType = "image/svg+xml";                  break;
                    case "ttf"   :                 $contentType = "application/x-font-ttf";         break;
                    case "otf"   :                 $contentType = "application/x-font-opentype";    break;
                    case "woff"  :                 $contentType = "application/font-woff";          break;
                    case "woff2" :                 $contentType = "application/font-woff2";         break;
                    case "eot"   :                 $contentType = "application/vnd.ms-fontobject";  break;
                    case "sfnt"  :                 $contentType = "application/font-sfnt";          break;

                    default      :                 $contentType = "application/octet-stream";       break;
                }

                // Set the response Content-Type header and write the contents of the file to the response body.
                $response = $response->withHeader("Content-Type", $contentType);
                $response->getBody()->write(file_get_contents($path));

                // Then return the response!
                return $response;
            }
        )->setName(AssetRoute::class);

    }

}
