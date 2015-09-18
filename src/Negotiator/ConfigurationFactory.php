<?php
namespace Gofabian\Middleware\Negotiator;

use InvalidArgumentException;
use Negotiation\Negotiator;
use Negotiation\LanguageNegotiator;
use Negotiation\EncodingNegotiator;
use Negotiation\CharsetNegotiator;
use Negotiation\Accept;
use Negotiation\AcceptLanguage;
use Negotiation\AcceptEncoding;
use Negotiation\AcceptCharset;

/**
 * Factory for instances of Configuration.
 */
class ConfigurationFactory
{

    private function __construct()
    {
    }

    /**
     * Create an instance of Configuration.
     *
     * @param $headerName       string          name of accept header
     * @param $priorities       array[string]   list of accepted values sorted by
     *                                          priority (first has highest)
     * @return Configuration
     */
    public static function create($headerName, array $priorities)
    {
        $negotiator = self::createNegotiator($headerName);
        $acceptFactory = self::createAcceptFactory($headerName);

        $c = new Configuration;
        $c->setHeaderName($headerName);
        $c->setPriorities($priorities);
        $c->setNegotiator($negotiator);
        $c->setAcceptFactory($acceptFactory);
        return $c;
    }

    private static function createNegotiator($headerName)
    {
        switch($headerName) {
            case 'accept':
                return new Negotiator;
            case 'accept-language':
                return new LanguageNegotiator;
            case 'accept-encoding':
                return new EncodingNegotiator;
            case 'accept-charset':
                return new CharsetNegotiator;
            default:
                throw new InvalidArgumentException('Unexpected header name: ' . $headerName);
        }
    }

    private static function createAcceptFactory($headerName)
    {
        switch($headerName) {
            case 'accept':
                return function($v) { return new Accept($v); };
            case 'accept-language':
                return function($v) { return new AcceptLanguage($v); };
            case 'accept-encoding':
                return function($v) { return new AcceptEncoding($v); };
            case 'accept-charset':
                return function($v) { return new AcceptCharset($v); };
            default:
                throw new InvalidArgumentException('Unexpected header name: ' . $headerName);
        }
    }

}
