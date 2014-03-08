<?php

use Psets\Interval;
use Psets\IntervalSet;

class IntervalTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_start = new DateTime('2014-01-01 00:00:00', new DateTimeZone('UTC'));
        $this->_end = new DateTime('2014-01-02 00:00:00', new DateTimeZone('UTC'));
    }

    public function test_interval_has_start_and_end()
    {
        $interval = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-02 00:00:00'));

        $this->assertEquals($this->_dt('2014-01-01 00:00:00'), $interval->getStart());
        $this->assertEquals($this->_dt('2014-01-02 00:00:00'), $interval->getEnd());
    }

    public function test_get_interval_period_should_return_in_seconds()
    {
        $interval = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-02 00:00:00'));
        $this->assertEquals(86400, $interval->getPeriod());
    }

    public function test_end_date_is_always_greater_than_or_equal_to_start_date()
    {
        $interval1 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-02 00:00:00'));
        $interval2 = new Interval($this->_dt('2014-01-02 00:00:00'), $this->_dt('2014-01-01 00:00:00'));

        $this->assertGreaterThanOrEqual($interval1->getStart(), $interval1->getEnd());
        $this->assertGreaterThanOrEqual($interval2->getStart(), $interval2->getEnd());
    }

    public function test_overlaps_should_be_true_if_two_intervals_overlap_each_other()
    {
        $interval1 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-02 00:00:00'));
        $interval2 = new Interval($this->_dt('2014-01-01 05:00:00'), $this->_dt('2014-01-01 20:00:00'));

        $this->assertTrue($interval1->overlaps($interval2));
        $this->assertTrue($interval2->overlaps($interval1));
    }


    public function test_overlaps_should_be_false_if_two_intervals_dont_overlap_each_other()
    {
        $interval1 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-02 00:00:00'));
        $interval2 = new Interval($this->_dt('2014-01-03 00:00:00'), $this->_dt('2014-01-04 00:00:00'));

        $this->assertFalse($interval1->overlaps($interval2));
        $this->assertFalse($interval2->overlaps($interval1));
    }

    public function test_overlaps_should_be_false_if_two_intervals_overlap_each_other_on_the_edge()
    {
        $interval1 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-02 00:00:00'));
        $interval2 = new Interval($this->_dt('2014-01-02 00:00:00'), $this->_dt('2014-01-03 00:00:00'));

        $this->assertFalse($interval1->overlaps($interval2));
        $this->assertFalse($interval2->overlaps($interval1));
    }


    public function test_diff_should_be_new_interval_from_main_interval_subtracted_by_second_interval()
    {
        // interval 1 ----------
        // interval 2      ----------
        // expected   -----
        $interval1 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 10:00:00'));
        $interval2 = new Interval($this->_dt('2014-01-01 05:00:00'), $this->_dt('2014-01-01 15:00:00'));
        $expected = new IntervalSet(new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 05:00:00')));
        $this->assertEquals($expected, $interval1->diff($interval2));

        // interval 1      ----------
        // interval 2 ----------
        // expected             -----
        $interval1 = new Interval($this->_dt('2014-01-01 05:00:00'), $this->_dt('2014-01-01 15:00:00'));
        $interval2 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 10:00:00'));
        $expected = new IntervalSet(new Interval($this->_dt('2014-01-01 10:00:00'), $this->_dt('2014-01-01 15:00:00')));
        $this->assertEquals($expected, $interval1->diff($interval2));

    }

    public function test_diff_should_be_empty_resultset_if_both_intervals_are_same()
    {
        // interval 1 ----------
        // interval 2 ----------
        // expected   
        $interval1 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 10:00:00'));
        $interval2 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 10:00:00'));
        $expected = new IntervalSet;
        $this->assertEquals($expected, $interval1->diff($interval2));

    }

    public function test_diff_should_be_main_interval_if_there_is_no_overlap()
    {
        // interval 1 -----
        // interval 2        -----
        // expected   -----
        $interval1 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 05:00:00'));
        $interval2 = new Interval($this->_dt('2014-01-01 07:00:00'), $this->_dt('2014-01-01 12:00:00'));
        $expected = new IntervalSet([new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 05:00:00'))]);
        $this->assertEquals($expected, $interval1->diff($interval2));

    }

    public function test_diff_should_return_intervalset_if_result_has_more_than_1_interval()
    {
        // interval 1 ---------------
        // interval 2      -----
        // expected   -----     -----
        $interval1 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 15:00:00'));
        $interval2 = new Interval($this->_dt('2014-01-01 05:00:00'), $this->_dt('2014-01-01 10:00:00'));
        $expected = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 05:00:00')),
            new Interval($this->_dt('2014-01-01 10:00:00'), $this->_dt('2014-01-01 15:00:00')),
        ]);
        $this->assertEquals($expected, $interval1->diff($interval2));

    }

    public function test_diff_should_return_empty_intervalset_main_interval_is_inside_second_interval()
    {
        // interval 1      -----
        // interval 2 ---------------
        // expected   
        $interval1 = new Interval($this->_dt('2014-01-01 05:00:00'), $this->_dt('2014-01-01 10:00:00'));
        $interval2 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 15:00:00'));
        $expected = new IntervalSet;
        $this->assertEquals($expected, $interval1->diff($interval2));

    }

    public function test_union_should_return_intervalset_aggregating_both_intervals()
    {
        // interval 1 -----
        // interval 2     -----
        // expected   ---------
        $interval1 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 06:00:00'));
        $interval2 = new Interval($this->_dt('2014-01-01 05:00:00'), $this->_dt('2014-01-01 10:00:00'));
        $expected = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 10:00:00')),
        ]);
        $this->assertEquals($expected, $interval1->union($interval2));

        // interval 1 -----
        // interval 2       -----
        // expected   ----- -----
        $interval1 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 05:00:00'));
        $interval2 = new Interval($this->_dt('2014-01-01 06:00:00'), $this->_dt('2014-01-01 11:00:00'));
        $expected = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 05:00:00')),
            new Interval($this->_dt('2014-01-01 06:00:00'), $this->_dt('2014-01-01 11:00:00')),
        ]);
        $this->assertEquals($expected, $interval1->union($interval2));

    }

    protected function _dt($timestamp, $timezone = 'UTC')
    {
        return new DateTime($timestamp, new DateTimeZone($timezone));
    }

}
