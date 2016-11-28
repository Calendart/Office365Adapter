<?php

namespace CalendArt\Adapter\Office365;

use Psr\Http\Message\ResponseInterface;

use CalendArt\Adapter\Office365\Exception\BadRequestException;

class ApiErrorExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithBadFormat()
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->shouldBeCalled()->willReturn('bad');
        $response->getStatusCode()->shouldBeCalled()->willReturn(400);
        $response->getReasonPhrase()->shouldBeCalled()->willReturn('Invalid Argument');

        $e = new BadRequestException($response->reveal());

        $this->assertEquals('The request failed and returned an invalid status code ("400") : Invalid Argument', $e->getMessage());
    }

    public function testConstructWithUnexceptedFormat()
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->shouldBeCalled()->willReturn(json_encode(['error' => []]));
        $response->getStatusCode()->shouldBeCalled()->willReturn(400);
        $response->getReasonPhrase()->shouldBeCalled()->willReturn('Invalid Argument');

        $e = new BadRequestException($response->reveal());

        $this->assertEquals('The request failed and returned an invalid status code ("400") : Invalid Argument', $e->getMessage());
    }

    public function testConstructWithExceptedFormat()
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->shouldBeCalled()->willReturn(json_encode(['error' => ['message' => 'Api Message']]));
        $response->getStatusCode()->shouldBeCalled()->willReturn(400);
        $response->getReasonPhrase()->shouldNotBeCalled();

        $e = new BadRequestException($response->reveal());

        $this->assertEquals('The request failed and returned an invalid status code ("400") : Api Message', $e->getMessage());
    }
}
