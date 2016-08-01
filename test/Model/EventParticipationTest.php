<?php

namespace CalendArt\Adapter\Office365\Model;

/**
 * Class EventParticipationTest
 * @package CalendArt\Adapter\Office365\Model
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class EventParticipationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider statusProvider()
     */
    public function testSetStatus($status)
    {
        $event = $this->prophesize("CalendArt\\AbstractEvent");
        $user = $this->prophesize("CalendArt\\User");
        $participation = new EventParticipation($event->reveal(), $user->reveal());
        $participation->setStatus($status);
    }

    public function statusProvider()
    {
        return [
            [EventParticipation::STATUS_NONE],
            [EventParticipation::STATUS_ORGANIZER],
            [EventParticipation::STATUS_TENTATIVE],
            [EventParticipation::STATUS_ACCEPTED],
            [EventParticipation::STATUS_DECLINED],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetStatusWithWrongStatusShouldThrowAnException()
    {
        $event = $this->prophesize("CalendArt\\AbstractEvent");
        $user = $this->prophesize("CalendArt\\User");
        $participation = new EventParticipation($event->reveal(), $user->reveal());
        $participation->setStatus(42);
    }

    public function testGetAvailableStatuses()
    {
        //assert same to distinguish between 0 and NULL
        self::assertSame(
            [
                EventParticipation::STATUS_DECLINED,
                EventParticipation::STATUS_TENTATIVE,
                EventParticipation::STATUS_ACCEPTED,
                EventParticipation::STATUS_NONE,
                EventParticipation::STATUS_ORGANIZER
            ],
            EventParticipation::getAvailableStatuses()
        );


    }
}
