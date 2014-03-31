<?php

namespace Psets;

class IntervalSet
{
    public $intervals = [];

    public function __construct($intervals = null)
    {
        if(!is_array($intervals)) {
            $intervals = [$intervals];
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
        $diffs = [];
        $thisIntervals = $this->intervals;
        $otherIntervals = $other->intervals;

        foreach ($thisIntervals as $thisInterval) {
            $currentDiffs = [];

            foreach ($otherIntervals as $otherInterval) {
                $currentDiffs[] = $thisInterval->diff($otherInterval);
            }

            $diffs[] = $currentDiffs;
        }

        $results  =[];
        foreach ($diffs as $diffGroup) {
            $groupDiffIntersection = null;
            foreach ($diffGroup as $diff) {
                if(!$groupDiffIntersection) {
                    $groupDiffIntersection = $diff;
                } else {
                    $groupDiffIntersection = $groupDiffIntersection->intersect($diff);
                }
            }

            $results[] = $groupDiffIntersection;
        }

        return new IntervalSet($results);
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
        $r = [];

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
