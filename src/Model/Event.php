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

namespace CalendArt\Adapter\Office365\Model;

use Datetime;
use DateTimeZone;

use Exception;
use InvalidArgumentException;

use Doctrine\Common\Collections\ArrayCollection;

use CalendArt\AbstractEvent;
use CalendArt\EventParticipation as BaseEventParticipation;

use CalendArt\Adapter\Office365\ReflectionTrait;
use CalendArt\Adapter\Office365\Office365Adapter;
use CalendArt\Adapter\Office365\WindowsTimezone;

/**
 * Office365
 *
 * @link https://msdn.microsoft.com/office/office365/APi/complex-types-for-mail-contacts-calendar#EventResource
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Event extends AbstractEvent
{
    use ReflectionTrait;

    const STATUS_UNKNOWN = -1;
    const STATUS_FREE = 0;
    const STATUS_TENTATIVE = 1;
    const STATUS_BUSY = 2;
    const STATUS_OOF = 3;
    const STATUS_WORKING_ELSEWHERE = 4;

    const IMPORTANCE_LOW = 0;
    const IMPORTANCE_NORMAL = 1;
    const IMPORTANCE_HIGH = 2;

    const TYPE_SINGLE_INSTANCE = 0;
    const TYPE_OCCURRENCE = 1;
    const TYPE_EXCEPTION = 2;
    const TYPE_SERIES_MASTER = 3;

    /** @var string */
    private $id;

    /** @var string[] */
    private $categories = [];

    /** @var integer */
    private $status = self::STATUS_UNKNOWN;

    /** @var string */
    private $etag;

    /** @var Datetime */
    private $createdAt;

    /** @var Datetime */
    private $updatedAt;

    /** @var Attachment */
    private $attachments = [];

    /** @var integer */
    private $importance = self::IMPORTANCE_NORMAL;

    /** @var string Where the event is supposed to happen */
    public $location;

    /** @var boolean */
    private $cancelled = false;

    /** @var Recurrence */
    private $recurrence;

    /** @var array */
    private $raw;

    public function __construct(Calendar $calendar = null)
    {
        $this->calendar = $calendar;

        if (null !== $calendar) {
            $calendar->getEvents()->add($this);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    /** @return Calendar */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /** @return Datetime */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /** @return Datetime */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /** @return string */
    public function getEtag()
    {
        return $this->etag;
    }

    /** @return string[] */
    public function getCategories()
    {
        return $this->categories;
    }

    /** @param string $category */
    public function addCategory($category)
    {
        $this->categories[] = $category;
        $this->categories = array_unique($this->categories);
    }

    /** @param string $category */
    public function removeCategory($category)
    {
        $key = array_search($category, $this->categories, true);

        if (false !== $key) {
            unset($this->categories[$key]);
            $this->categories = array_values($this->categories);
        }
    }

    /** @return integer */
    public function getStatus()
    {
        return $this->status;
    }

    /** @param integer $status */
    public function setStatus($status)
    {
        if (!in_array($status, [static::STATUS_FREE, static::STATUS_TENTATIVE, static::STATUS_BUSY, static::STATUS_OOF, static::STATUS_WORKING_ELSEWHERE])) {
            throw new InvalidArgumentException('Wrong status');
        }

        $this->status = $status;
    }

    /** @return integer */
    public function getImportance()
    {
        return $this->importance;
    }

    /** @param integer $importance */
    public function setImportance($importance)
    {
        if (!in_array($importance, [static::IMPORTANCE_LOW, static::IMPORTANCE_NORMAL, static::IMPORTANCE_HIGH])) {
            throw new InvalidArgumentException('Wrong importance');
        }

        $this->importance = $importance;
    }

    /** @return boolean */
    public function isCancelled()
    {
        return true === $this->cancelled;
    }

    /** @return self */
    public function cancel()
    {
        $this->cancelled = true;

        return $this;
    }

    /** @return boolean */
    public function isRecurrent()
    {
        return null !== $this->recurrence;
    }

    /** @return Recurrence */
    public function getRecurrence()
    {
        return $this->recurrence;
    }

    /** @return integer */
    public function getType()
    {
        return $this->type;
    }

    /** @param integer $type */
    public function setType($type)
    {
        if (!in_array($type, [static::TYPE_SINGLE_INSTANCE, static::TYPE_OCCURRENCE, static::TYPE_EXCEPTION, static::TYPE_SERIES_MASTER])) {
            throw new InvalidArgumentException('Wrong type');
        }

        $this->type = $type;
    }

    /** @return $this */
    public function addParticipation(BaseEventParticipation $participation)
    {
        if (!$participation instanceof EventParticipation) {
            throw new InvalidArgumentException('Only a Office365 EventParticipation may be added as an attendee to an Office365 Event');
        }

        return parent::addParticipation($participation);
    }

    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * Hydrate a new Event object with data received from Office365 api
     *
     * @param array $data Data to feed the Event object with
     * @return self
     */
    public static function hydrate(array $data, Calendar $calendar = null)
    {
        if (!isset($data['id'], $data['changeKey'])) {
            throw new InvalidArgumentException(sprintf('Missing at least one of the mandatory properties "id", "changeKey" ; got ["%s"]', implode('", "', array_keys($data))));
        }

        $event = new static($calendar);

        $event->id = $data['id'];
        $event->etag = $data['changeKey'];
        $event->raw = $data;

        // if subject is not set or is null, we use null as name
        $event->name = isset($data['subject']) ? $data['subject'] : null;

        if (!empty($data['bodyPreview'])) {
            $event->description = $data['bodyPreview'];
        }

        if (isset($data['location']) && isset($data['location']['displayName'])) {
            $event->location = $data['location']['displayName'];
        }

        $event->createdAt = new Datetime($data['createdDateTime']);
        $event->updatedAt = new Datetime($data['lastModifiedDateTime']);

        $windowsTimezone = new WindowsTimezone();
        $startTimeZone = new DateTimeZone('UTC');
        $endTimeZone = new DateTimeZone('UTC');

        try {
            $endTimeZone = new DateTimeZone($windowsTimezone->getTimezone($data['end']['timeZone']));
        } catch (Exception $e) { }

        try {
            $startTimeZone = new DateTimeZone($windowsTimezone->getTimezone($data['start']['timeZone']));
        } catch (Exception $e) { }

        if (isset($data['start']) && isset($data['start']['dateTime'])) {
            $event->start = new Datetime($data['start']['dateTime'], $startTimeZone);
        }

        if (isset($data['end']) && isset($data['end']['dateTime'])) {
            $event->end = new Datetime($data['end']['dateTime'], $endTimeZone);
        }

        $event->recurrence = $data['recurrence'];
        $event->allDay = true === $data['isAllDay'];
        $event->cancelled = true === $data['isCancelled'];

        $event->categories = $data['categories'];

        $event->importance = self::translateConstantToValue('IMPORTANCE_', $data['importance']);
        $event->status = self::translateConstantToValue('STATUS_', $data['showAs']);
        $event->type = self::translateConstantToValue('TYPE_', $data['type']);

        $event->owner = Office365Adapter::buildUser($data['organizer']);
        $event->owner->addEvent($event);

        $event->participations = new ArrayCollection;
        $attendees = isset($data['attendees']) ? $data['attendees'] : [];

        //now the fun stuff : the attendees
        foreach ($attendees as $attendee) {
            // a resource is not an attendee
            if ('resource' === $attendee['type']) {
                continue;
            }

            $user = Office365Adapter::buildUser($attendee);
            $role = EventParticipation::ROLE_PARTICIPANT;

            if ($event->owner->getId() === $user->getId()) {
                $role |= EventParticipation::ROLE_MANAGER;
            }

            $participation = new EventParticipation($event, $user, $role, EventParticipation::STATUS_NONE);
            if (isset($attendee['status'])) {
                $participation->setStatus(EventParticipation::translateStatus($attendee['status']['response']));

                if (EventParticipation::STATUS_NONE !== $participation->getStatus()) {
                    $participation->setAnsweredAt(new Datetime($attendee['status']['time']));
                }
            }
            $participation->setType(EventParticipation::translateType($attendee['type']));

            $user->addEvent($event);
            $event->participations->add($participation);
        }

        return $event;
    }
}
