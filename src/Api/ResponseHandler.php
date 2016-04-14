<?php

namespace CalendArt\Adapter\Office365\Api;

use GuzzleHttp\Message\ResponseInterface;

use CalendArt\Adapter\Office365\Exception;

trait ResponseHandler
{
    /**
     * @param ResponseInterface $response
     *
     * @throws Exception\BadRequestException
     * @throws Exception\UnauthorizedException
     * @throws Exception\ForbiddenException
     * @throws Exception\NotFoundException
     * @throws Exception\MethodNotAllowedException
     * @throws Exception\ConflictException
     * @throws Exception\GoneException
     * @throws Exception\PreconditionException
     * @throws Exception\LimitExceededException
     * @throws Exception\InternalServerErrorException
     */
    private function handleResponse(ResponseInterface $response)
    {
        $statusCode = (int) $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            return;
        }

        switch ($statusCode) {
            case 400:
            case 406:
            case 411:
            case 413:
            case 415:
            case 416:
            case 422:
                throw new Exception\BadRequestException($response);

            case 401:
                throw new Exception\UnauthorizedException($response);

            case 403:
                throw new Exception\ForbiddenException($response);

            case 404:
            case 501:
                throw new Exception\NotFoundException($response);

            case 405:
                throw new Exception\MethodNotAllowedException($response);

            case 409:
                throw new Exception\ConflictException($response);

            case 410:
                throw new Exception\GoneException($response);

            case 412:
                throw new Exception\PreconditionException($response);

            case 429:
            case 507:
            case 509:
                throw new Exception\LimitExceededException($response);

            case 500:
            case 503:
            default:
                throw new Exception\InternalServerErrorException($response);
        }
    }
}
