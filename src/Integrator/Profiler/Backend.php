<?php

namespace algoweb\Profiler\Integrator\Profiler;


/**
 * Low-level abstraction for storage of profiling data.
 */
interface Backend
{
    public function socketStore(array $trace);
    public function udpStore(array $trace);
}