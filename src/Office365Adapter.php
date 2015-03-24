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

namespace CalendArt\Adapter\Office365;

use GuzzleHttp\Client as Guzzle;

use CalendArt\Adapter\AdapterInterface,

    CalendArt\Adapter\Office365\Api,
    CalendArt\Adapter\Office365\Model\User;

/**
 * Office365 Adapter - He knows how to dialog with office 365's calendars !
 *
 * This requires to have an OAuth2 token established with the following scopes :
 * - Calendar.read
 * - Calendar.write
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Office365Adapter implements AdapterInterface
{
    /** @var User[] All the fetched and hydrated users, with an id as a key **/
    private static $users = [];

    /** @param string $token access token delivered by azure's oauth system */
    public function __construct($token)
    {
        $this->guzzle = new Guzzle(['base_url' => 'https://outlook.office365.com/api/v1.0/me/',
                                    'defaults' => ['exceptions' => false,
                                                    'headers' => ['Authorization' => sprintf('Bearer %s', $token),
                                                                  'Content-Type' => 'application/json',
                                                                  'Accept' => 'application/json']]]);
    }

    /** {@inheritDoc} */
    public function getCalendarApi()
    {
        static $api = null;

        if (null === $api) {
            $api = new Api\CalendarApi($this->guzzle, $this);
        }

        return $api;
    }

    /** {@inheritDoc} */
    public function getEventApi()
    {
        static $api = null;

        if (null === $api) {
            $api = new Api\EventApi($this->guzzle, $this);
        }

        return $api;
    }

    /**
     * Build a User object based on given data
     *
     * @param array $data User data
     *
     * @return User
     */
    public static function buildUser(array $data)
    {
        $id = sha1($data['EmailAddress']['Address']);

        if (!isset(static::$users[$id])) {
            static::$users[$id] = User::hydrate($data['EmailAddress']);
        }

        return static::$users[$id];
    }
}

