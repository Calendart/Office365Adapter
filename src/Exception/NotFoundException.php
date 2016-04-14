<?php

namespace CalendArt\Adapter\Office365\Exception;

use CalendArt\Exception\NotFoundInterface;

class NotFoundException extends ApiErrorException implements NotFoundInterface
{
}
