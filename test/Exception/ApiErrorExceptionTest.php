<?php

namespace CalendArt\Adapter\Office365;

use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Exception\ParseException;

use CalendArt\Adapter\Office365\Exception\BadRequestException;

class ApiErrorExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithParseException()
    {
        $response = $this->prophesize('GuzzleHttp\Message\ResponseInterface');
        $response->json()->shouldBeCalled()->willThrow(new ParseException);
        $response->getStatusCode()->shouldBeCalled()->willReturn(400);
        $response->getReasonPhrase()->shouldBeCalled()->willReturn('Invalid Argument');

        $e = new BadRequestException($response->reveal());

        $this->assertEquals('The request failed and returned an invalid status code ("400") : Invalid Argument', $e->getMessage());
    }

    public function testConstructWithUnexceptedFormat()
    {
        $response = $this->prophesize('GuzzleHttp\Message\ResponseInterface');
        $response->json()->shouldBeCalled()->willReturn(['error' => []]);
        $response->getStatusCode()->shouldBeCalled()->willReturn(400);
        $response->getReasonPhrase()->shouldBeCalled()->willReturn('Invalid Argument');

        $e = new BadRequestException($response->reveal());

        $this->assertEquals('The request failed and returned an invalid status code ("400") : Invalid Argument', $e->getMessage());
    }

    public function testConstructWithExceptedFormat()
    {
        $response = $this->prophesize('GuzzleHttp\Message\ResponseInterface');
        $response->json()->shouldBeCalled()->willReturn(['error' => ['message' => 'Api Message']]);
        $response->getStatusCode()->shouldBeCalled()->willReturn(400);
        $response->getReasonPhrase()->shouldNotBeCalled();

        $e = new BadRequestException($response->reveal());

        $this->assertEquals('The request failed and returned an invalid status code ("400") : Api Message', $e->getMessage());
    }
}
