<?php
/**
 * This file is part of the CalendArt package
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 *
 * @copyright Wisembly
 * @license   http://www.opensource.org/licenses/MIT-License MIT License
 */

namespace CalendArt\Adapter\Office365\Exception;

use ErrorException;

use Psr\Http\Message\ResponseInterface;

use CalendArt\Exception\ApiErrorInterface;

/**
 * Whenever the Api returns an unexpected result
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
abstract class ApiErrorException extends ErrorException implements ApiErrorInterface
{
    private $details;

    public function __construct(ResponseInterface $response)
    {
        $this->details = json_decode($response->getBody(), true);
        $message = isset($this->details['error']['message']) ? $this->details['error']['message'] : $response->getReasonPhrase();

        parent::__construct(sprintf('The request failed and returned an invalid status code ("%d") : %s', $response->getStatusCode(), $message), $response->getStatusCode());
    }

    public function getDetails()
    {
        return $this->details;
    }
}
