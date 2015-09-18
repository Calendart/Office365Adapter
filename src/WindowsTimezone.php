<?php

namespace CalendArt\Adapter\Office365;

use CalendArt\Adapter\Office365\Exception\InvalidTimezoneException;
use CalendArt\Adapter\Office365\Exception\TimezoneNotFoundException;

/**
 * Translate a windows timezone to a IANA timezone
 * Windows timezone : https://msdn.microsoft.com/en-us/library/ms912391%28v=winembedded.11%29.aspx
 * IANA timezone : https://www.iana.org/time-zones or https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
 *
 * @author Manuel Raynaud <manuel@wisembly.com>
 */
class WindowsTimezone
{
    protected static $timezone = [
        'Dateline Standard Time'                    => 'Antarctica/McMurdo',
        'Samoa Standard Time'                       => 'Pacific/Apia',
        'Hawaiian Standard Time'                    => 'Pacific/Honolulu',
        'Alaskan Standard Time'                     => 'America/Anchorage',
        'Pacific Standard Time'                     => 'America/Los_Angeles',
        'Mountain Standard Time'                    => 'America/Phoenix',
        'Mexico Standard Time 2'                    => 'America/Chihuahua',
        'U.S. Mountain Standard Time'               => 'America/Denver',
        'Central Standard Time'                     => 'America/Chicago',
        'Canada Central Standard Time'              => 'America/Regina',
        'Mexico Standard Time'                      => 'America/Mexico_City',
        'Central America Standard Time'             => 'America/Guatemala',
        'Eastern Standard Time'                     => 'America/New_York',
        'U.S. Eastern Standard Time'                => 'America/Indiana/Indianapolis',
        'S.A. Pacific Standard Time'                => 'America/Bogota',
        'Atlantic Standard Time'                    => 'America/Halifax',
        'S.A. Western Standard Time'                => 'America/La_Paz',
        'Pacific S.A. Standard Time'                => 'America/Santo_Domingo',
        'Newfoundland and Labrador Standard Time'   => 'America/St_Johns',
        'E. South America Standard Time'            => 'America/Sao_Paulo',
        'S.A. Eastern Standard Time'                => 'America/Argentina/Buenos_Aires',
        'Greenland Standard Time'                   => 'America/Godthab',
        'Mid-Atlantic Standard Time'                => 'America/Noronha',
        'Azores Standard Time'                      => 'Atlantic/Azores',
        'Cape Verde Standard Time'                  => 'Atlantic/Cape_Verde',
        'GMT Standard Time'                         => 'Europe/London',
        'Greenwich Standard Time'                   => 'Africa/Abidjan',
        'Central Europe Standard Time'              => 'Europe/Belgrade',
        'Central European Standard Time'            => 'Europe/Warsaw',
        'Romance Standard Time'                     => 'Europe/Paris',
        'W. Europe Standard Time'                   => 'Europe/Berlin',
        'W. Central Africa Standard Time'           => 'Africa/Lagos',
        'E. Europe Standard Time'                   => 'Europe/Bucharest',
        'Egypt Standard Time'                       => 'Africa/Cairo',
        'FLE Standard Time'                         => 'Europe/Kiev',
        'GTB Standard Time'                         => 'Europe/Athens',
        'Israel Standard Time'                      => 'Asia/Jerusalem',
        'South Africa Standard Time'                => 'Africa/Johannesburg',
        'Russian Standard Time'                     => 'Europe/Moscow',
        'Arab Standard Time'                        => 'Asia/Riyadh',
        'E. Africa Standard Time'                   => 'Africa/Nairobi',
        'Arabic Standard Time'                      => 'Asia/Baghdad',
        'Iran Standard Time'                        => 'Asia/Tehran',
        'Arabian Standard Time'                     => 'Asia/Dubai',
        'Caucasus Standard Time'                    => 'Asia/Yerevan',
        'Transitional Islamic State of Afghanistan Standard Time' => 'Asia/Kabul',
        'Ekaterinburg Standard Time'                => 'Asia/Yekaterinburg',
        'West Asia Standard Time'                   => 'Asia/Tashkent',
        'India Standard Time'                       => 'Asia/Colombo',
        'Nepal Standard Time'                       => 'Asia/Colombo', //fallback on India Standard Time because can be absent on outdated database
        'Central Asia Standard Time'                => 'Asia/Almaty',
        'Sri Lanka Standard Time'                   => 'Asia/Colombo',
        'N. Central Asia Standard Time'             => 'Asia/Novosibirsk',
        'Myanmar Standard Time'                     => 'Asia/Rangoon',
        'S.E. Asia Standard Time'                   => 'Asia/Bangkok',
        'North Asia Standard Time'                  => 'Asia/Krasnoyarsk',
        'China Standard Time'                       => 'Asia/Shanghai',
        'Singapore Standard Time'                   => 'Asia/Singapore',
        'Taipei Standard Time'                      => 'Asia/Taipei',
        'W. Australia Standard Time'                => 'Australia/Perth',
        'North Asia East Standard Time'             => 'Asia/Irkutsk',
        'Korea Standard Time'                       => 'Asia/Seoul',
        'Tokyo Standard Time'                       => 'Asia/Tokyo',
        'Yakutsk Standard Time'                     => 'Asia/Yakutsk',
        'A.U.S. Central Standard Time'              => 'Australia/Darwin',
        'Cen. Australia Standard Time'              => 'Australia/Adelaide',
        'A.U.S. Eastern Standard Time'              => 'Australia/Sydney',
        'E. Australia Standard Time'                => 'Australia/Brisbane',
        'Tasmania Standard Time'                    => 'Australia/Hobart',
        'Vladivostok Standard Time'                 => 'Asia/Vladivostok',
        'West Pacific Standard Time'                => 'Pacific/Port_Moresby',
        'Central Pacific Standard Time'             => 'Pacific/Guadalcanal',
        'Fiji Islands Standard Time'                => 'Pacific/Fiji',
        'New Zealand Standard Time'                 => 'Pacific/Auckland',
        'Tonga Standard Time'                       => 'Pacific/Tongatapu'
    ];

    /**
     * return a valid IANA timezone
     *
     * @param string $windowsTimezone timezone in widows format
     *
     * @return string
     */
    public function getTimezone($windowsTimezone)
    {
        if (!isset(self::$timezone[$windowsTimezone])) {
            throw new TimezoneNotFoundException(sprintf('windows timezone %s is not registered', $windowsTimezone));
        }

        if (!in_array(self::$timezone[$windowsTimezone], \DateTimeZone::listIdentifiers())) {
            throw new InvalidTimezoneException(sprintf("the timezone %s does not exists in your installation", self::$timezone[$windowsTimezone]));
        }

        return self::$timezone[$windowsTimezone];
    }
}
