<?php

use Psets\Interval;
use Psets\IntervalSet;

class IntervalSetTest extends PHPUnit_Framework_TestCase
{
    public function test_intervalset_is_always_ordered()
    {
        $i1 = new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 05:00:00'));
        $i2 = new Interval($this->_dt('2014-01-01 07:00:00'), $this->_dt('2014-01-01 09:00:00'));
        $i3 = new Interval($this->_dt('2014-01-01 10:00:00'), $this->_dt('2014-01-01 15:00:00'));
        $is1 = new IntervalSet([$i1, $i3, $i2]);
        $is2 = new IntervalSet([$i3, $i2, $i1]);

        $this->assertEquals($is1, $is2);
    }

    public function test_intervalset_collapses_intervals_that_overlap_each_other()
    {
        $is1 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 06:00:00'), $this->_dt('2014-01-01 08:00:00')),
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 02:00:00')),
            new Interval($this->_dt('2014-01-01 02:00:00'), $this->_dt('2014-01-01 04:00:00')),
            new Interval($this->_dt('2014-01-01 10:00:00'), $this->_dt('2014-01-01 12:00:00')),
            new Interval($this->_dt('2014-01-01 12:00:00'), $this->_dt('2014-01-01 14:00:00')),
        ]);

        $is2 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 04:00:00')),
            new Interval($this->_dt('2014-01-01 06:00:00'), $this->_dt('2014-01-01 08:00:00')),
            new Interval($this->_dt('2014-01-01 10:00:00'), $this->_dt('2014-01-01 14:00:00')),
        ]);

        $this->assertEquals($is2, $is1);
    }

    public function test_intervalset_collapses_when_one_interval_overlaps_all_others()
    {
        $is1 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 02:00:00'), $this->_dt('2014-01-01 04:00:00')),
            new Interval($this->_dt('2014-01-01 02:00:00'), $this->_dt('2014-01-01 04:00:00')),
            new Interval($this->_dt('2014-01-01 04:00:00'), $this->_dt('2014-01-01 06:00:00')),
            new Interval($this->_dt('2014-01-01 06:00:00'), $this->_dt('2014-01-01 08:00:00')),
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 10:00:00')),
        ]);

        $is2 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 10:00:00')),
        ]);

        $this->assertEquals($is2, $is1);
    }

    public function test_union_intervalsets_that_dont_overlap()
    {
        $is1 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 01:00:00')),
        ]);

        $is2 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 04:00:00'), $this->_dt('2014-01-01 05:00:00')),
        ]);

        $expected = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 01:00:00')),
            new Interval($this->_dt('2014-01-01 04:00:00'), $this->_dt('2014-01-01 05:00:00')),
        ]);

        $is1->union($is2);

        $this->assertEquals($expected, $is1);
    }

    public function test_union_intervalsets_with_one_overlap()
    {
        $is1 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 01:00:00')),
        ]);

        $is2 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 01:00:00'), $this->_dt('2014-01-01 02:00:00')),
        ]);

        $expected = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 02:00:00')),
        ]);

        $is1->union($is2);

        $this->assertEquals($expected, $is1);
    }

    public function test_union_intervalsets_with_many_overlap()
    {
        $is1 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 01:00:00')),
            new Interval($this->_dt('2014-01-01 10:00:00'), $this->_dt('2014-01-01 11:00:00')),
        ]);

        $is2 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 01:00:00'), $this->_dt('2014-01-01 02:00:00')),
            new Interval($this->_dt('2014-01-01 11:00:00'), $this->_dt('2014-01-01 12:00:00')),
        ]);

        $expected = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 02:00:00')),
            new Interval($this->_dt('2014-01-01 10:00:00'), $this->_dt('2014-01-01 12:00:00')),
        ]);

        $is1->union($is2);

        $this->assertEquals($expected, $is1);
    }

    public function test_diff($value='')
    {
        // intervalset 1 ---  ---
        // intervalset 2   ---- ---
        // diff          --    -
        $is1 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 03:00:00')),
            new Interval($this->_dt('2014-01-01 05:00:00'), $this->_dt('2014-01-01 08:00:00')),
        ]);

        $is2 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 02:00:00'), $this->_dt('2014-01-01 06:00:00')),
            new Interval($this->_dt('2014-01-01 07:00:00'), $this->_dt('2014-01-01 10:00:00')),
        ]);

        $expected = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 02:00:00')),
            new Interval($this->_dt('2014-01-01 06:00:00'), $this->_dt('2014-01-01 07:00:00')),
        ]);

        $this->assertEquals($expected, $is1->diff($is2));
    }

    public function test_intersect()
    {
        // intervalset 1 ---  ---
        // intervalset 2   ---- ---
        // diff            -  - -
        $is1 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 03:00:00')),
            new Interval($this->_dt('2014-01-01 05:00:00'), $this->_dt('2014-01-01 08:00:00')),
        ]);

        $is2 = new IntervalSet([
            new Interval($this->_dt('2014-01-01 02:00:00'), $this->_dt('2014-01-01 06:00:00')),
            new Interval($this->_dt('2014-01-01 07:00:00'), $this->_dt('2014-01-01 10:00:00')),
        ]);

        $expected = new IntervalSet([
            new Interval($this->_dt('2014-01-01 02:00:00'), $this->_dt('2014-01-01 03:00:00')),
            new Interval($this->_dt('2014-01-01 05:00:00'), $this->_dt('2014-01-01 06:00:00')),
            new Interval($this->_dt('2014-01-01 07:00:00'), $this->_dt('2014-01-01 08:00:00')),
        ]);

        $this->assertEquals($expected, $is1->intersect($is2));
        $this->assertEquals($expected, $is2->intersect($is1));
    }

    public function test_get_intervalset_length()
    {
        // intervalset ---  ---
        $is = new IntervalSet([
            new Interval($this->_dt('2014-01-01 00:00:00'), $this->_dt('2014-01-01 03:00:00')),
            new Interval($this->_dt('2014-01-01 05:00:00'), $this->_dt('2014-01-01 08:00:00')),
        ]);

        $expected = 6*3600;

        $this->assertEquals($expected, $is->getLength());
    }

    public function test_get_intervalset_length_should_return_zero_when_intervalset_is_empty()
    {
        $is = new IntervalSet;
        $this->assertEquals(0, $is->getLength());
    }

    protected function _dt($timestamp, $timezone = 'UTC')
    {
        return new DateTime($timestamp, new DateTimeZone($timezone));
    }

}
