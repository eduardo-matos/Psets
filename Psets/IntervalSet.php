<?php

namespace Psets;

class IntervalSet
{
    public $intervals = array();

    public function __construct($intervals = null)
    {
        if(!$intervals) {
            return;
        }

        if(!is_array($intervals)) {
            $intervals = array($intervals);
        }

        $orderedIntervals = $this->_order($intervals);
        $collapsedIntervals = $this->_collapse($orderedIntervals);

        $this->intervals = $collapsedIntervals;
    }

    public function union(IntervalSet $other)
    {
        $int = array_merge($this->intervals, $other->intervals);
        $orderedIntervals = $this->_order($int);
        $collapsedIntervals = $this->_collapse($orderedIntervals);

        $this->intervals = $collapsedIntervals;
    }

    public function diff(IntervalSet $other)
    {
        $diffs = array();
        $thisIntervals = $this->intervals;
        $otherIntervals = $other->intervals;

        foreach ($thisIntervals as $thisInterval) {
            $currentDiffs = array();

            foreach ($otherIntervals as $otherInterval) {
                $result = $thisInterval->diff($otherInterval);
                $currentDiffs[] = $result instanceof IntervalSet? $result: new IntervalSet($result);
            }

            $diffs[] = $currentDiffs;
        }

        $intervalSetResults = array();
        foreach ($diffs as $diffGroup) {
            $groupDiffIntersection = null;
            foreach ($diffGroup as $diff) {
                if(!$groupDiffIntersection) {
                    $groupDiffIntersection = $diff;
                } else {
                    $intersectionResult = $groupDiffIntersection->intersect($diff);
                    $groupDiffIntersection = $intersectionResult;
                }

            }

            $intervalSetResults[] = $groupDiffIntersection;
        }

        $intersectionUnion = null;
        foreach ($intervalSetResults as $intervalSet) {
            if(!$intersectionUnion) {
                $intersectionUnion = $intervalSet;
            } else {
                $intersectionUnion->union($intervalSet);
            }
        }

        return $intersectionUnion;
    }

    public function intersect(IntervalSet $other)
    {
        $results = array();
        foreach ($this->intervals as $thisInterval) {
            foreach ($other->intervals as $otherInterval) {
                $currentIntersect = $thisInterval->intersect($otherInterval);
                if($currentIntersect) {
                    $results[] = $currentIntersect;
                }
            }
        }

        return new IntervalSet($results);
    }

    public function getLength()
    {
        $totalLength = 0;

        foreach ($this->intervals as $interval) {
            $totalLength += $interval->getLength();
        }

        return $totalLength;
    }

    public function _order($intervals)
    {
        usort($intervals, function ($one, $other)
        {
            if($one == $other) {
                return 0;
            }

            return $one->getStart() < $other->getStart()? -1: 1;
        });

        return $intervals;
    }

    public function _collapse($intervals)
    {
        $r = array();

        foreach ($intervals as $key => $interval) {
            if($key === 0) {
                $r[] = $interval;
                continue;
            }

            $baseKey = count($r) - 1;
            $baseValue = end($r);

            if($interval->overlaps($baseValue) OR $interval->isAdjacent($baseValue)) {
                $r[$baseKey] = $interval->union($baseValue);
            } else {
                $r[] = $interval;
            }
        }

        return $r;
    }
}
