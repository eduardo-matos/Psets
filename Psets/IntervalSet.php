<?php

namespace Psets;

class IntervalSet
{
    protected $_intervals = [];

    public function __construct($intervals = null)
    {
        if(is_array($intervals)) {
            $this->_intervals = $intervals;
        } else if ($intervals instanceof Interval) {
            $this->_intervals = [$intervals];
        }
    }
}
