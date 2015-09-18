<?php
namespace Gofabian\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Negotiation\AbstractNegotiator;
use Negotiation\BaseAccept;
use Gofabian\Middleware\Negotiator\Configuration;
use Gofabian\Middleware\Negotiator\ConfigurationFactory;
use Gofabian\Middleware\Negotiator\HeaderNegotiator;
use Gofabian\Middleware\Negotiator\AcceptProvider;
use Gofabian\Middleware\Negotiator\NegotiationException;

/**
 * The class Negotiator is a middleware service originally written for the Slim
 * framework.
 *
 * @see https://github.com/slimphp/Slim/tree/3.x
 */
class Negotiator
{
    private $headerNegotiator;
    private $supplyDefaults;
    private $attributeName;

    private $mediaTypeConf;
    private $languageConf;
    private $encodingConf;
    private $charsetConf;

    /**
     * Create a new negotiator middleware that negotiates media type, language,
     * encoding and charset for the response.
     */
    public function __construct(
        array $priorities,
        $supplyDefaults = true,
        $attributeName = 'negotiation'
    ) {
        $this->headerNegotiator = new HeaderNegotiator;
        $this->supplyDefaults = $supplyDefaults;
        $this->attributeName = $attributeName;

        $this->mediaTypeConf = $this->createConfiguration($priorities, 'accept');
        $this->languageConf = $this->createConfiguration($priorities, 'accept-language');
        $this->encodingConf = $this->createConfiguration($priorities, 'accept-encoding');
        $this->charsetConf = $this->createConfiguration($priorities, 'accept-charset');
    }

    private function createConfiguration($allPriorities, $headerName)
    {
        if (!empty($allPriorities[$headerName])) {
            $priorities = $allPriorities[$headerName];
            return ConfigurationFactory::create($headerName, $priorities);
        }
        return null;
    }


    /**
     *
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        try {
            $acceptProvider = $this->negotiateRequest($request);
        } catch (NegotiationException $e) {
            return $response->withStatus(406);
        }

        $request = $request->withAttribute($this->attributeName, $acceptProvider);
        return $next($request, $response);
    }

    /**
     * Negotiate the PSR-7 request.
     *
     * @param $request  ServerRequestInterface  the PSR-7 request
     * @return          AcceptProvider          the negotiation result
     * @throws          NegotiationException    negotiation failed
     */
    private function negotiateRequest(ServerRequestInterface $request)
    {
        $mediaType = $this->negotiateHeader($request, $this->mediaTypeConf);
        $language = $this->negotiateHeader($request, $this->languageConf);
        $encoding = $this->negotiateHeader($request, $this->encodingConf);
        $charset = $this->negotiateHeader($request, $this->charsetConf);

        return new AcceptProvider($mediaType, $language, $encoding, $charset);
    }

    /**
     * Negotiate the header configured in <code>$conf</code>.
     *
     * Returns <code>null</code> if the configuration is <code>null</code>.
     *
     * @param $request  ServerRequestInterface  the PSR-7 request
     * @param $conf     Configuration|null      the header configuration
     * @return          BaseAccept|null         the negotiation result
     * @throws          NegotiationException    negotiation failed
     */
    private function negotiateHeader(ServerRequestInterface $request, Configuration $conf = null)
    {
        if (is_null($conf)) {
            // no negotiation configuration
            return null;
        }
        return $this->headerNegotiator->negotiate($request, $conf, $this->supplyDefaults);
    }

}
