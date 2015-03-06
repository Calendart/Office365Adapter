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

use CalendArt\AbstractCalendar;

class Calendar extends AbstractCalendar
{
    /** @var string Calendar's id */
    private $id;

    /**
     * @var string Calendar's Etag
     *
     * The etag is called a "ChangeKey" in Office365. The real etag
     * is the ChangeKey prefixed, so it is basically the same thing ?
     */
    private $etag;

    /** @return string */
    public function getId()
    {
        return $this->id;
    }

    public function getEtag()
    {
        return $this->etag;
    }

    public static function hydrate(array $data)
    {
        if (!isset($data['Id'], $data['Name'], $data['ChangeKey'])) {
            throw new InvalidArgumentException(sprintf('Missing at least one of the mandatory properties "Id", "Name", "ChangeKey" ; got ["%s"]', implode('", "', array_keys($data))));
        }

        $calendar = new static($data['Name']);
        $calendar->id = $data['Id'];
        $calendar->etag = $data['ChangeKey'];

        return $calendar;
    }
}

