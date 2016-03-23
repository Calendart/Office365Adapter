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

use CalendArt\User as BaseUser;

/**
 * Office365 "User"
 *
 * @link https://msdn.microsoft.com/office/office365/APi/complex-types-for-mail-contacts-calendar#EmailAddress
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class User extends BaseUser
{
    /** @var string */
    private $id;

    /**
     * Raw data which built this object
     *
     * @var array
     */
    private $raw = [];

    /** @return string */
    public function getId()
    {
        return $this->id;
    }

    /** @return array */
    public function getRawData()
    {
        return $this->raw;
    }

    public static function hydrate(array $data)
    {
        if (!isset($data['address'], $data['name'])) {
            throw new InvalidArgumentException(sprintf('Missing some required key (required : [\'address\', \'name\'], got [\'%s\'])', array_keys($data)));
        }

        $user = new static($data['name'], $data['address']);

        $user->raw = $data;
        $user->id = sha1($data['address']);

        return $user;
    }
}

