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

use ReflectionClass,
    InvalidArgumentException;

/**
 * Basic set of reflection utils
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
trait ReflectionTrait
{
    /**
     * Transform a front name using constant into a value
     *
     * @param string $prefix  Constant prefix to ignore and serve as a comparison basis
     * @param scalar constant Constant to use to find the value
     *
     * @return string Value transformed
     * @throws InvalidArgumentException If the constant was not found
     */
    private static function translateConstantToValue($prefix, $constant)
    {
        static $constants = null;

        if (null === $constants) {
            $refl      = new ReflectionClass(__CLASS__);
            $constants = $refl->getConstants();
        }

        $prefix = strtoupper($prefix);
        $prefixLength = strlen($prefix);

        // uncamelize constant
        $constant = preg_replace_callback('`[a-z0-9][A-Z]`', function (array $match) { return sprintf('%s_%s', $match[0][0], $match[0][1]); }, $constant);
        $constant = strtoupper($constant);

        foreach ($constants as $name => $value) {
            if ($prefix === substr($name, 0, $prefixLength) && substr($name, $prefixLength) === $constant) {
                return $value;
            }
        }
        throw new InvalidArgumentException('Constant value not found');
    }
}
