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

namespace CalendArt\Adapter\Office365\Api;

use GuzzleHttp\Client as Guzzle;

use Doctrine\Common\Collections\ArrayCollection;

use CalendArt\Adapter\CalendarApiInterface;

use CalendArt\Adapter\Office365\Model\Calendar;
use CalendArt\Adapter\Office365\Office365Adapter;

/**
 * Office365 API for the Calendars
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class CalendarApi implements CalendarApiInterface
{
    use ResponseHandler;

    /** @var Guzzle Guzzle Http Client to use */
    private $guzzle;

    /** @var Office365Adapter Office365 Adapter used */
    private $adapter;

    public function __construct(Guzzle $client, Office365Adapter $adapter)
    {
        $this->guzzle  = $client;
        $this->adapter = $adapter;
    }

    /** {@inheritDoc} */
    public function getList()
    {
        $request = $this->guzzle->createRequest('GET', 'calendars');
        $response = $this->guzzle->send($request);

        $this->handleResponse($response);

        $result = $response->json();
        $list = new ArrayCollection;

        foreach ($result['value'] as $item) {
            $list[$item['id']] = Calendar::hydrate($item);
        }

        return $list;
    }

    public function get($identifier)
    {
        $request = $this->guzzle->createRequest('GET', sprintf('calendars/%s', $identifier));
        $response = $this->guzzle->send($request);

        $this->handleResponse($response);

        return Calendar::hydrate($response->json());
    }
}

