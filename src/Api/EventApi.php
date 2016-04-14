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

use DateTime;
use InvalidArgumentException;

use Doctrine\Common\Collections\ArrayCollection;

use CalendArt\AbstractEvent;
use CalendArt\Adapter\EventApiInterface;

use CalendArt\Adapter\Office365\Model\Event;
use CalendArt\Adapter\Office365\Model\Calendar;

use CalendArt\Adapter\Office365\Office365Adapter;

/**
 * Office365 API for the Calendars
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class EventApi implements EventApiInterface
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

    private function requestEvents($url, array $params = [])
    {
        $request = $this->guzzle->createRequest('GET', $url, $params);
        $response = $this->guzzle->send($request);

        $this->handleResponse($response);

        $result = $response->json();
        $list = new ArrayCollection;

        foreach ($result['value'] as $item) {
            $list[$item['id']] = Event::hydrate($item);
        }

        return $list;
    }

    /**
     * {@inheritDoc}
     *
     * If a calendar is given, it fetches the event for this calendar ; otherwise, it takes the primary
     *
     * @param string $filter `$filter` query parameter to give to the request
     * @param string $orderBy `$orderBy` query, to have an order of elements
     */
    public function getList(Calendar $calendar = null, $filter = '', $orderBy = '', array $extraParameters = [])
    {
        $url = 'events';

        if (null !== $calendar) {
            $url = sprintf('calendars/%s/events', $calendar->getId());
        }

        $params = $extraParameters;

        if (!empty($filter)) {
            $params['$filter'] = $filter;
        }

        if (!empty($orderBy)) {
            $params['$orderBy'] = $orderBy;
        }

        if (!empty($params)) {
            $params = ['query' => $params];
        }

        return $this->requestEvents($url, $params);
    }

    /**
     * Get the occurrences, exceptions, and single instances of events in a calendar view
     *
     * If a calendar is given, it fetches the events for this calendar ;
     * otherwise, it takes the primary
     *
     * @param DateTime $from The date and time when the event starts.
     * @param DateTime $to The date and time when the event ends.
     * @param string $filter `$filter` query parameter to give to the request
     * @param string $orderBy `$orderBy` query, to have an order of elements
     *
     * @see https://msdn.microsoft.com/office/office365/APi/calendar-rest-operations#GetCalendarView
     */
    public function getCalendarView(
        Calendar $calendar = null,
        DateTime $from,
        DateTime $to,
        $filter = '',
        $orderBy = '',
        array $extraParameters = []
    ) {
        $url = 'calendarview';

        if (null !== $calendar) {
            $url = sprintf('calendars/%s/calendarview', $calendar->getId());
        }

        $params = [
            'startDateTime' => $from->format('Y-m-d\TH:i:s\Z'),
            'endDateTime' => $to->format('Y-m-d\TH:i:s\Z'),
        ];

        if (!empty($filter)) {
            $params['$filter'] = $filter;
        }

        if (!empty($orderBy)) {
            $params['$orderBy'] = $orderBy;
        }

        $params = ['query' => array_merge($params, $extraParameters)];

        return $this->requestEvents($url, $params);
    }

    /** {@inheritDoc} */
    public function get($identifier)
    {
        $request = $this->guzzle->createRequest('GET', sprintf('events/%s', $identifier), []);
        $response = $this->guzzle->send($request);

        $this->handleResponse($response);

        return Event::hydrate($response->json());
    }

    /** {@inheritDoc} */
    public function persist(AbstractEvent $event, array $options = [])
    {
        if (!$event instanceof Event) {
            throw new InvalidArgumentException('Wrong event provided, expected an office event');
        }

        $params = [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($event->export())
        ];

        $method = 'POST';
        $url = sprintf('calendars/%s/events', $event->getCalendar()->getId());
        if (null !== $event->getId()) {
            $method = 'PATCH';
            $url = sprintf('%s/%s', $url, $event->getId());
        }

        $request = $this->guzzle->createRequest($method, $url, $params);
        $response = $this->guzzle->send($request);

        if (200 > $response->getStatusCode() || 300 <= $response->getStatusCode()) {
            throw new ApiErrorException($response);
        }

        return Event::hydrate($response->json());
    }
}
