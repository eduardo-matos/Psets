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
        if($this->getEnd() <= $other->getStart() OR $other->getEnd() <= $this->getStart()) {
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

        if($this->overlaps($other) OR $this->isAdjacent($other)) {
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

    public function isAdjacent(Interval $other)
    {
        $thisStart = $this->getStart();
        $thisEnd = $this->getEnd();
        $otherStart = $other->getStart();
        $otherEnd = $other->getEnd();

        if($this->overlaps($other)) {
            return false;
        } else if($thisStart == $otherEnd OR $otherStart == $thisEnd) {
            return true;
        }

        return false;
    }

    public function comesBefore(Interval $other)
    {
        $thisStart = $this->getStart();
        $thisEnd = $this->getEnd();
        $otherStart = $other->getStart();
        $otherEnd = $other->getEnd();

        if($this->isAdjacent($other) AND $thisStart < $otherStart) {
            return true;
        } else if($this->overlaps($other)) {
            return false;
        } else if($thisStart > $otherEnd OR $otherStart > $thisEnd) {
            return true;
        }

        return false;
    }

    public function intersect(Interval $other)
    {
        $thisStart = $this->getStart();
        $thisEnd = $this->getEnd();
        $otherStart = $other->getStart();
        $otherEnd = $other->getEnd();

        if(!$this->overlaps($other)) {
            return false;
        }

        if($thisEnd > $otherEnd AND $thisStart < $otherStart) {
            // this contains other
            return clone($other);
        } else if($otherEnd > $thisEnd AND $otherStart < $thisStart) {
            // other contains this
            return clone($this);
        }

        if($thisStart < $otherStart) {
            $intersectionStart = $otherStart;
            $intersectionEnd = $thisEnd;
        } else {
            $intersectionStart = $thisStart;
            $intersectionEnd = $otherEnd;
        }

        return new Interval($intersectionStart, $intersectionEnd);
    }
}
