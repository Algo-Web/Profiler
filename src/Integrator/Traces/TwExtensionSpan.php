<?php

namespace algoweb\Profiler\Integrator\Traces;


class TwExtensionSpan extends Span
{
    /**
     * @var int
     */
    private $idx;

    public function createSpan($name = null)
    {
        return new self(algoweb_span_create($name));
    }

    public function getSpans()
    {
        return algoweb_get_spans();
    }

    public function __construct($idx)
    {
        $this->idx = $idx;
    }

    /**
     * 32/64 bit random integer.
     *
     * @return int
     */
    public function getId()
    {
        return $this->idx;
    }

    /**
     * Record start of timer in microseconds.
     *
     * If timer is already running, don't record another start.
     */
    public function startTimer()
    {
        algoweb_span_timer_start($this->idx);
    }

    /**
     * Record stop of timer in microseconds.
     *
     * If timer is not running, don't record.
     */
    public function stopTimer()
    {
        algoweb_span_timer_stop($this->idx);
    }

    /**
     * Annotate span with metadata.
     *
     * @param array<string,scalar>
     */
    public function annotate(array $annotations)
    {
        algoweb_span_annotate($this->idx, $annotations);
    }
}