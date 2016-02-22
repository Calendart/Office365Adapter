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

use CalendArt\UriAttachment;

class FileAttachment implements UriAttachment
{
    private $name;
    private $uri;
    private $content;
    private $id;
    private $size;
    private $contentType;
    private $raw;

    public function __construct($name, $uri)
    {
        $this->name = $name;
        $this->uri = $uri;
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

        if ('#Microsoft.OutlookServices.FileAttachment' !== $data['@odata.type']) {
            throw new InvalidArgumentException(sprintf('Unknown attachment type %s', $data['@odata.type']));
        }

        $attachment = new static($data['Name'], $data['ContentLocation']);
        $attachment->id = $data['Id'];
        $attachment->content = $data['ContentBytes'];
        $attachment->size = $data['Size'];
        $attachment->contentType = $data['ContentType'];
        $attachment->raw = $data;

        return $attachment;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getSize()
    {
        return $this->size;
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
        return $this->content;
    }
}
