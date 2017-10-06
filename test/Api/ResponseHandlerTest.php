<?php

namespace CalendArt\Adapter\Office365\Api;

use PHPUnit\Framework\TestCase;

use Psr\Http\Message\ResponseInterface;

class ResponseHandlerTest extends TestCase
{
    private $response;
    private $api;

    protected function setUp()
    {
        $this->response = $this->prophesize(ResponseInterface::class);
        $this->api = new Api;
    }

    public function testHandleErrorsWithSuccessfulResponse()
    {
        $this->response->getStatusCode()->shouldBeCalled()->willReturn(200);
        $this->api->get($this->response->reveal());

        $this->response->getStatusCode()->shouldBeCalled()->willReturn(301);
        $this->api->get($this->response->reveal());
    }

    /**
     * @dataProvider getResponses
     */
    public function testHandleErrors($statusCode, $exception)
    {
        $this->setExpectedException($exception);

        $this->response->getStatusCode()->shouldBeCalled()->willReturn($statusCode);
        $this->response->getBody()->shouldBeCalled()->willReturn(json_encode(['error' => ['message' => 'foo']]));
        $this->api->get($this->response->reveal());
    }

    public function getResponses()
    {
        return [
            [400,'CalendArt\Adapter\Office365\Exception\BadRequestException'],
            [401,'CalendArt\Adapter\Office365\Exception\UnauthorizedException'],
            [403,'CalendArt\Adapter\Office365\Exception\ForbiddenException'],
            [404,'CalendArt\Adapter\Office365\Exception\NotFoundException'],
            [405,'CalendArt\Adapter\Office365\Exception\MethodNotAllowedException'],
            [406,'CalendArt\Adapter\Office365\Exception\BadRequestException'],
            [409,'CalendArt\Adapter\Office365\Exception\ConflictException'],
            [410,'CalendArt\Adapter\Office365\Exception\GoneException'],
            [411,'CalendArt\Adapter\Office365\Exception\BadRequestException'],
            [412,'CalendArt\Adapter\Office365\Exception\PreconditionException'],
            [413,'CalendArt\Adapter\Office365\Exception\BadRequestException'],
            [415,'CalendArt\Adapter\Office365\Exception\BadRequestException'],
            [416,'CalendArt\Adapter\Office365\Exception\BadRequestException'],
            [422,'CalendArt\Adapter\Office365\Exception\BadRequestException'],
            [429,'CalendArt\Adapter\Office365\Exception\LimitExceededException'],
            [500,'CalendArt\Adapter\Office365\Exception\InternalServerErrorException'],
            [501,'CalendArt\Adapter\Office365\Exception\NotFoundException'],
            [503,'CalendArt\Adapter\Office365\Exception\InternalServerErrorException'],
            [507,'CalendArt\Adapter\Office365\Exception\LimitExceededException'],
            [509,'CalendArt\Adapter\Office365\Exception\LimitExceededException'],
        ];
    }
}

class Api
{
    use ResponseHandler;

    /**
     * Simulate a get method of an API
     */
    public function get(ResponseInterface $response)
    {
        $this->handleResponse($response);
    }
}
