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

use InvalidArgumentException;

use CalendArt\Attachement;

class FileAttachment implements UriAttachment
{
    private $id;
    private $name;
    private $contentType;
    private $item;

    public function __construct($name, Event $event)
    {
        $this->event = $event;
        $this->name = $name;
    }

    /**
     * Hydrate a new Event object with data received from Office365 api
     *
     * @param array $data Data to feed the Event object with
     * @return CalendArt\Attachment
     */
    public static function hydrate(array $data)
    {
        if (!isset($data['Id'], $data['@odata.type'])) {
            throw new InvalidArgumentException(sprintf('Missing at least one of the mandatory properties "Id", "@odata.type" ; got ["%s"]', implode('", "', array_keys($data))));
        }

        if ('#Microsoft.OutlookServices.ItemAttachment' !== $data['@odata.type']) {
            throw new InvalidArgumentException(sprintf('Unknown attachment type %s', $data['@odata.type']));
        }

        $attachment = new static($data['Name'], Event::hydrate($data['Event']));
        $attachment->id = $data['Id'];
        $attachment->contentType = $data['ContentType'];
        $attachment->raw = $data;

        return $attachment;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public function getContents()
    {
        return $this->getEvent();
    }
}
