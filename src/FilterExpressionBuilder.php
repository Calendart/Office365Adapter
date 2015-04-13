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

/**
 * This class is used to generate a filter query parameter for Office365 apis via PHP functions
 *
 * This is based on the Expr of the Doctrine ORM package
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class FilterExpressionBuilder
{
    /**
     * Returns a `and` expression
     *
     * Needs at least one parameter, but this is a variadic function
     *
     * @return string
     */
    public static function andX($x)
    {
        return implode(' and ', array_filter(func_get_args()));
    }

    /**
     * Returns a `or` expression
     *
     * Needs at least one parameter, but this is a variadic function
     *
     * @return string
     */
    public static function orX($x)
    {
        return implode(' or ', array_filter(func_get_args()));
    }

    /**
     * Returns a `eq` expression
     *
     * @param string $x left expression
     * @param string $y right expression
     *
     * @return string
     */
    public static function eq($x, $y)
    {
        return sprintf('%1$s eq %2$s', $x, $y);
    }

    /**
     * Returns a `ne` expression
     *
     * @param string $x left expression
     * @param string $y right expression
     *
     * @return string
     */
    public static function neq($x, $y)
    {
        return sprintf('%1$s ne %2$s', $x, $y);
    }

    /**
     * Returns a `gt` expression
     *
     * @param string $x left expression
     * @param string $y right expression
     *
     * @return string
     */
    public static function gt($x, $y)
    {
        return sprintf('%1$s gt %2$s', $x, $y);
    }

    /**
     * Returns a `ge` expression
     *
     * @param string $x left expression
     * @param string $y right expression
     *
     * @return string
     */
    public static function gte($x, $y)
    {
        return sprintf('%1$s ge %2$s', $x, $y);
    }

    /**
     * Returns a `lt` expression
     *
     * @param string $x left expression
     * @param string $y right expression
     *
     * @return string
     */
    public static function lt($x, $y)
    {
        return sprintf('%1$s lt %2$s', $x, $y);
    }

    /**
     * Returns a `le` expression
     *
     * @param string $x left expression
     * @param string $y right expression
     *
     * @return string
     */
    public static function lte($x, $y)
    {
        return sprintf('%1$s le %2$s', $x, $y);
    }
}

