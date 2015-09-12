<?php

use NegotiationMiddleware\Negotiator;
use Slim\Http\Response;

class NegotiatorTest extends PHPUnit_Framework_TestCase {

    private $request;
    private $response;

    public function setUp() {
        $this->request = $this->getMockBuilder('\Slim\Http\Request')
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->response = new Response;
    }


    public function testEmptyHeaderAndSupplyDefaultButNoPriorities() {
        $this->prepareAcceptHeader('');

        $result = $this->invokeMiddleware([], true);
        $this->verifyNotAccepted($result);
    }

    public function testEmptyHeaderAndSupplyDefault() {
        $this->prepareAcceptHeader('');
        $this->expectNegotiation('text/html');

        $result = $this->invokeMiddleware(['text/html'], true);
    }

    public function testNegotiationSuccess() {
        $this->prepareAcceptHeader('application/json, text/xml');
        $this->expectNegotiation('text/xml');

        $result = $this->invokeMiddleware(['text/xml'], true);
    }

    public function testNegotiationFails() {
        $this->prepareAcceptHeader('application/json, text/xml');

        $result = $this->invokeMiddleware(['text/html'], true);

        $this->verifyNotAccepted($result);
    }

    public function testNoSupplyDefault() {
        $this->prepareAcceptHeader('');

        $result = $this->invokeMiddleware(['text/html'], false);

        $this->verifyNotAccepted($result);
    }


    private function prepareAcceptHeader($accept) {
        $this->request->method('getHeaderLine')
                      ->with($this->equalTo('accept'))
                      ->willReturn($accept);
    }

    private function expectNegotiation($mediaType) {
        $this->request->expects($this->once())
                      ->method('withAttribute')
                      ->with(
                            $this->equalTo('negotiation.mediaType'),
                            $this->callback(function($accept) use ($mediaType) {
                                return $accept->getValue() === $mediaType;
                            })
                        )
                      ->willReturn($this->request);
    }

    private function invokeMiddleware($priorities, $supplyDefault) {
        $negotiator = new Negotiator($priorities, $supplyDefault);
        return $negotiator->__invoke($this->request, $this->response, function($request, $response) {
            return $response;
        });
    }

    private function verifyNotAccepted($response) {
        $this->assertEquals(406, $response->getStatusCode());
    }

}
