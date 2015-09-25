<?php
namespace Gofabian\Negotiation;

use Negotiation\Accept;
use Negotiation\AcceptLanguage;
use Negotiation\AcceptEncoding;
use Negotiation\AcceptCharset;

/**
 * The AcceptProvider offers one instance of \Negotiation\Accept for each
 * HTTP accept header. The result will be null if the dedicated accept header
 * is not configured.
 *
 * @see https://github.com/willdurand/Negotiation
 */
class AcceptProvider {

    private $acceptMediaType;
    private $acceptLanguage;
    private $acceptEncoding;
    private $acceptCharset;

    public function __construct(Accept $acceptMediaType = null, AcceptLanguage $acceptLanguage = null,
        AcceptEncoding $acceptEncoding = null, AcceptCharset $acceptCharset = null) {
        $this->acceptMediaType = $acceptMediaType;
        $this->acceptLanguage = $acceptLanguage;
        $this->acceptEncoding = $acceptEncoding;
        $this->acceptCharset = $acceptCharset;
    }

    /**
     * @return \Negotiation\Accept|null the accepted content type
     */
    public function getAccept() {
        return $this->acceptMediaType;
    }

    /**
     * @return \Negotiation\Accept|null the accepted charset
     */
    public function getAcceptCharset() {
        return $this->acceptCharset;
    }

    /**
     * @return \Negotiation\Accept|null the accepted encoding
     */
    public function getAcceptEncoding() {
        return $this->acceptEncoding;
    }

    /**
     * @return \Negotiation\Accept|null the accepted language
     */
    public function getAcceptLanguage() {
        return $this->acceptLanguage;
    }

    /**
     * @return \Negotiation\Accept|null the accepted content type
     */
    public function getMediaType() {
        return $this->toText($this->acceptMediaType);
    }

    /**
     * @return \Negotiation\Accept|null the accepted charset
     */
    public function getCharset() {
        return $this->toText($this->acceptCharset);
    }

    /**
     * @return \Negotiation\Accept|null the accepted encoding
     */
    public function getEncoding() {
        return $this->toText($this->acceptEncoding);
    }

    /**
     * @return \Negotiation\Accept|null the accepted language
     */
    public function getLanguage() {
        return $this->toText($this->acceptLanguage);
    }

    private function toText($accept) {
        return empty($accept) ? '' : $accept->getValue();
    }

}
