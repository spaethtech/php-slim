<?php
declare(strict_types=1);

namespace MVQN\Slim\Routes;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

use Slim\App;

/**
 * Class AssetController
 *
 * Handles routing and provision of static assets.
 *
 * @package UCRM\Slim\Controllers\Common
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
                    // Assemble some standard data to send along to the 404 page for debugging!
                    $data = [
                        "route" => $request->getAttribute("vRoute"),
                        "query" => $request->getAttribute("vQuery"),
                        "user"  => $request->getAttribute("user"),
                    ];

                    // NOTE: Inside any route closure, $this refers to the Application's Container.
                    /** @var Container $container */
                    $container = $this;

                    // Return the default 404 page!
                    return $container->get("notFoundHandler")($request, $response, $data);
                    //return $response->withStatus(404, "Asset '$file.$ext' not found!");
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

        /*
        if($authenticators !== null)
        {
            // NOTE: However, outside the route closure, $this refers to the current object like usual!
            $route->add(new AuthenticationHandler($app->getContainer()));

            if(!is_array($authenticators))
                $authenticators = [ $authenticators ];

            foreach($authenticators as $authenticator)
            {
                if(is_a($authenticator, Authenticator::class))
                    $route->add($authenticator);
            }
        }
        */
    }

}