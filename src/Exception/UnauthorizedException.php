<?php

namespace CalendArt\Adapter\Office365\Exception;

use CalendArt\Exception\InvalidCredentialsInterface;

class UnauthorizedException extends ApiErrorException implements InvalidCredentialsInterface
{
}
