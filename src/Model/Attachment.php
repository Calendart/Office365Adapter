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

abstract class Attachment
{
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

        if ('#Microsoft.OutlookServices.FileAttachment' === $data['@odata.type']) {
            return FileAttachment::hydrate($data);
        }

        if ('#Microsoft.OutlookServices.ItemAttachment' === $data['@odata.type']) {
            return ItemAttachment::hydrate($data);
        }

        throw new InvalidArgumentException(sprintf('Unknown attachment type %s', $data['@odata.type']));
    }
}
