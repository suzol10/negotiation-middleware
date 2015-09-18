<?php
namespace Gofabian\Middleware\Negotiator;

use PHPUnit_Framework_TestCase;

class HeaderNegotiatorTest extends PHPUnit_Framework_TestCase
{

    private $negotiator;
    private $request;

    public function setUp()
    {
        $this->negotiator = new HeaderNegotiator;
        $this->request = new TestRequest;
    }

    public function testEmptyHeaderThrowsException()
    {

    }

}
