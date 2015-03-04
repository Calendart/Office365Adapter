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

    /** @var string Calendar's changekey */
    private $changeKey;

    /** @return string */
    public function getId()
    {
        return $this->id;
    }

    public function getChangeKey()
    {
        return $this->changeKey;
    }
}

