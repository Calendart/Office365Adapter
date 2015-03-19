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

use CalendArt\AbstractEvent,
    CalendArt\Adapter\EventApiInterface,

    CalendArt\Adapter\Office365\Model\Event,
    CalendArt\Adapter\Office365\Model\Calendar,

    CalendArt\Adapter\Office365\Office365Adapter,
    CalendArt\Adapter\Office365\Exception\ApiErrorException;

/**
 * Office365 API for the Calendars
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class EventApi implements EventApiInterface
{
    /** @var Guzzle Guzzle Http Client to use */
    private $guzzle;

    /** @var Office365Adapter Office365 Adapter used */
    private $adapter;

    public function __construct(Guzzle $client, Office365Adapter $adapter)
    {
        $this->guzzle  = $client;
        $this->adapter = $adapter;
    }

    /**
     * {@inheritDoc}
     *
     * If a calendar is given, it fetches the event for this calendar ; otherwise, it takes the primary
     */
    public function getList(Calendar $calendar = null)
    {
        $url = 'events';

        if (null !== $calendar) {
            $url = sprintf('calendars/%s/events', $calendar->getId());
        }

        $request = $this->guzzle->createRequest('GET', $url);
        $response = $this->guzzle->send($request);

        if (200 > $response->getStatusCode() || 300 <= $response->getStatusCode()) {
            throw new ApiErrorException($response);
        }

        $result = $response->json();
        $list = new ArrayCollection;

        foreach ($result['value'] as $item) {
            $list[$item['Id']] = Event::hydrate($item);
        }

        return $list;
    }

    /** {@inheritDoc} */
    public function get($identifier)
    {
    }

    /** {@inheritDoc} */
    public function persist(AbstractEvent $event)
    {
    }
}

