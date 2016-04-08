<?php

namespace CalendArt\Adapter\Office365\Model;

use Datetime;
use InvalidArgumentException;

use CalendArt\EventParticipation as BaseEventParticipation;

use CalendArt\Adapter\Office365\Model\Event;

class PartialEvent extends Event
{
    /** @var array */
    private $changedProperties = [];

    public function __construct(Calendar $calendar, $id = null)
    {
        parent::__construct($calendar);

        if (null !== $id) {
            $this->id = $id;
            $this->changedProperties['id'] = true;
        }
    }

    public function setEtag($etag)
    {
        $this->changedProperties['changeKey'] = true;
        $this->etag = $etag;
    }

    /** {@inheritDoc} */
    public function setStatus($status)
    {
        $this->changedProperties['status'] = true;
        parent::setStatus($status);
    }

    /** {@inheritDoc} */
    public function setName($name)
    {
        $this->changedProperties['subject'] = true;
        parent::setName($name);
    }

    /** {@inheritDoc} */
    public function setDescription($description)
    {
        $this->changedProperties['bodyPreview'] = true;
        parent::setDescription($description);
    }

    /** {@inheritDoc} */
    public function setStart(DateTime $start)
    {
        $this->changedProperties['start'] = true;
        parent::setStart($start);
    }

    /** {@inheritDoc} */
    public function setEnd(DateTime $end)
    {
        $this->changedProperties['end'] = true;
        parent::setEnd($end);
    }

    /** {@inheritDoc} */
    public function addParticipation(BaseEventParticipation $participation)
    {
        $this->changedProperties['attendees'] = true;
        parent::addParticipation($participation);
    }

    /** {@inheritDoc} */
    public function export()
    {
        $parentExport = parent::export();
        $export = [];
        foreach ($parentExport as $property => $value) {
            if (!isset($this->changedProperties[$property]) || true !== $this->changedProperties[$property]) {
                continue;
            }
            $export[$property] = $value;
        }
        return $export;
    }
}
