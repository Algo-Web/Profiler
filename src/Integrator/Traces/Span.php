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
     * Create Child span.
     * @private
     * @param null|mixed $name
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
    abstract public function getId();

    /**
     * Record start of timer in microseconds.
     *
     * If timer is already running, don't record another start.
     */
    abstract public function startTimer();

    /**
     * Record stop of timer in microseconds.
     *
     * If timer is not running, don't record.
     */
    abstract public function stopTimer();

    /**
     * Annotate span with metadata.
     *
     * @param array<string,scalar>
     */
    abstract public function annotate(array $annotations);
}
