<?php

namespace Calendart\Adapter\Office365;

use CalendArt\Adapter\Office365\WindowsTimezone;

/**
 * Class WindowsTimezoneTest
 * @package Calendart\Adapter\Office365\Test
 * @author Manuel Raynaud <manuel@wisembly.com>
 */
class WindowsTimezoneTest extends \PHPUnit_Framework_TestCase
{
    public function testValidRegisteredTimezone()
    {
        $timezoneList = \DateTimeZone::listIdentifiers();

        $windowsTimezone = new WindowsTimezone();

        $reflection = new \ReflectionProperty($windowsTimezone, 'timezone');
        $reflection->setAccessible(true);

        $registeredTimezone = array_values($reflection->getValue($windowsTimezone));

        $intersect = array_intersect($registeredTimezone, $timezoneList);

        $this->assertEquals($intersect, $registeredTimezone);
    }

    /**
     * @expectedException \CalendArt\Adapter\Office365\Exception\TimezoneNotFoundException
     */
    public function testGetTimezoneWithUnregisteredTimezone()
    {
        $windowsTimezone = new WindowsTimezone();
        $windowsTimezone->getTimezone('foo');
    }

    public function testGetTimezoneWithRegisteredTimezone()
    {
        $windowsTimezone = new WindowsTimezone();
        $timeZone = $windowsTimezone->getTimezone('Samoa Standard Time');

        $this->assertEquals('Pacific/Apia', $timeZone);
    }
}
