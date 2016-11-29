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
    /** @var Office365Adapter Office365 Adapter used */
    private $adapter;

    public function __construct(Office365Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /** {@inheritDoc} */
    public function getList()
    {
        $result = $this->adapter->sendRequest('get', '/calendars');
        $list = new ArrayCollection;

        foreach ($result['value'] as $item) {
            $list[$item['id']] = Calendar::hydrate($item);
        }

        return $list;
    }

    public function get($identifier)
    {
        $result = $this->adapter->sendRequest('get', sprintf('/calendars/%s', $identifier));
        return Calendar::hydrate($result);
    }
}
