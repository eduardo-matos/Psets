<?php

namespace Psets;

class Interval
{
    protected $_start;
    protected $_end;

    public function __construct(\DateTime $start, \DateTime $end)
    {
        if($end < $start) {
            list($start, $end) = [$end, $start];
        }

        $this->_start = $start;
        $this->_end = $end;

    }

    public function getStart()
    {
        return $this->_start;
    }

    public function getEnd()
    {
        return $this->_end;
    }

    public function getPeriod()
    {
        return $this->_end->format('U') - $this->_start->format('U');
    }

    public function overlaps(Interval $other)
    {
        if($this->getEnd() < $other->getStart() OR $other->getEnd() < $this->getStart()) {
            return false;
        } else {
            return $this->getEnd() > $other->getStart() OR $other->getEnd() > $this->getStart();
        }
    }

    public function diff(Interval $other)
    {
        if(!$this->overlaps($other)) {
            return clone($this);
        }

        $thisStart = clone($this->getStart());
        $thisEnd = clone($this->getEnd());
        $otherStart = clone($other->getStart());
        $otherEnd = clone($other->getEnd());

        if($thisStart < $otherStart AND $thisEnd > $otherEnd) {
            return new IntervalSet([
                new Interval($thisStart, $otherStart),
                new Interval($otherEnd, $thisEnd),
            ]);
        }

        if($thisStart >= $otherStart AND $thisEnd <= $otherEnd) {
            return new IntervalSet;
        }

        if($thisStart <= $otherStart) {
            return new Interval($thisStart, $otherStart);
        } else {
            return new Interval($otherEnd, $thisEnd);
        }

    }

    public function union(Interval $other)
    {
        $thisStart = clone($this->getStart());
        $thisEnd = clone($this->getEnd());
        $otherStart = clone($other->getStart());
        $otherEnd = clone($other->getEnd());

        // overlaps or touch on begin or end
        if($this->overlaps($other)) {
            $start = $thisStart < $otherStart? $thisStart: $otherStart;
            $end = $thisEnd > $otherEnd? $thisEnd: $otherEnd;
            return new Interval($start, $end);
        }

        if($thisEnd < $otherStart) {
            return new IntervalSet([
                new Interval($thisStart, $thisEnd),
                new Interval($otherStart, $otherEnd),
            ]);
        } else {
            return new IntervalSet([
                new Interval($otherStart, $otherEnd),
                new Interval($thisStart, $thisEnd),
            ]);            
        }
    }
}
