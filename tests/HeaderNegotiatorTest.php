<?php declare(strict_types=1);
namespace Gofabian\Negotiation;

use Negotiation\AcceptCharset;
use Negotiation\Negotiator;

use RuntimeException;
use PHPUnit\Framework\TestCase;

class HeaderNegotiatorTest extends TestCase
{

    private $negotiator;
    private $request;

    public function setUp(): void
    {
        $this->negotiator = new HeaderNegotiator;
        $this->request = new TestRequest;
    }


    public function testEmptyHeaderThrowsException()
    {
        $this->expectException(NegotiationException::class);

        $conf = $this->createConfiguration('accept-type');
        $this->request->withHeader('accept-type', '');

        $this->negotiate($conf, false);
    }

    public function testEmptyHeaderResultsInDefault()
    {
        $conf = $this->createConfiguration('accept-lang', ['value']);
        $conf->setAcceptFactory(function($v) {
            return new AcceptCharset($v);
        });
        $this->request->withHeader('accept-lang', null);

        $this->verifyNegotiation($conf, true, 'value');
    }

    public function testSuccessfulNegotiation()
    {
        $conf = $this->createConfiguration('accept', ['text/html']);
        $conf->setNegotiator(new Negotiator);
        $this->request->withHeader('accept', 'text/html');

        $this->verifyNegotiation($conf, false, 'text/html');
    }

    public function testNegotiationError()
    {
        $this->expectException(NegotiationException::class);
        $this->expectExceptionMessage('negotiator error');

        $conf = $this->createConfiguration('accept', ['gogogo']);
        $n = $this->createMock('\Negotiation\Negotiator');
        $n->method('getBest')
            ->will($this->throwException(new RuntimeException));
        $conf->setNegotiator($n);
        $this->request->withHeader('accept', 'not empty');

        $this->negotiate($conf, false);
    }


    private function createConfiguration($headerName, array $priorities = [''])
    {
        $conf = new Configuration;
        $conf->setHeaderName($headerName);
        $conf->setPriorities($priorities);
        return $conf;
    }

    private function verifyNegotiation(Configuration $conf, $supplyDefault, $expectedResult)
    {
        $result = $this->negotiate($conf, $supplyDefault);

        $this->assertEquals($expectedResult, $result->getValue());
    }

    private function negotiate(Configuration $conf, $supplyDefault)
    {
        return $this->negotiator->negotiate($this->request, $conf, $supplyDefault);
    }

}
