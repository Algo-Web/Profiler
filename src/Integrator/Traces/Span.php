<?php

namespace algoweb\Profiler\Integrator\Traces;

/**
 * Abstraction for trace spans.
 *
 * Different implementations based on support
 */
abstract class Span
{
    /**
     * Create Child span
     * @private
     */
    abstract public function createSpan($name = null);

    /**
     * @private
     * @return array
     */
    abstract public function getSpans();

    /**
     * 32/64 bit random integer.
     *
     * @return int
     */
    public abstract function getId();

    /**
     * Record start of timer in microseconds.
     *
     * If timer is already running, don't record another start.
     */
    public abstract function startTimer();

    /**
     * Record stop of timer in microseconds.
     *
     * If timer is not running, don't record.
     */
    public abstract function stopTimer();

    /**
     * Annotate span with metadata.
     *
     * @param array<string,scalar>
     */
    public abstract function annotate(array $annotations);
}