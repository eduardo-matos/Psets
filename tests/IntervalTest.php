<?php

use Psets\Interval;

class IntervalTest extends PHPUnit_Framework_TestCase
{
    public function test_interval_has_start_and_end()
    {
        $start = new DateTime('2014-01-01 00:00:00');
        $end = new DateTime('2014-01-05 00:00:00');
        $interval = new Interval($start, $end);

        $this->assertEquals($interval->getStart(), $start);
        $this->assertEquals($interval->getEnd(), $end);
    }

    public function test_get_interval_period_should_return_in_seconds()
    {
        $start = new DateTime('2014-01-01 00:00:00');
        $end = new DateTime('2014-01-02 00:00:00');
        $interval = new Interval($start, $end);

        $this->assertEquals($interval->getPeriod(), 86400);
    }
}
