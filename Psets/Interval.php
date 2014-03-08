<?php

namespace Psets;

class Interval
{
    protected $_start;
    protected $_end;

    public function __construct(\DateTime $start, \DateTime $end)
    {
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
        $intervalo = $this->_end->diff($this->_start);
        return $intervalo->days * 60 * 60 *  24;
    }
}
