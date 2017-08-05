<?php

namespace algoweb\Profiler\Integrator\Profiler;

/**
 * Convert a Backtrace to a String like {@see Exception::getTraceAsString()} would do.
 */
class BacktraceConverter
{
    static public function convertToString(array $backtrace)
    {
        $trace = '';

        foreach ($backtrace as $k => $v) {
            if (!isset($v['function'])) {
                continue;
            }

            if (!isset($v['file'])) {
                $v['file'] = '';
            }

            if (!isset($v['line'])) {
                $v['line'] = '';
            }

            $args = '';
            if (isset($v['args'])) {
                $args = implode(', ', array_map(function ($arg) {
                    return (is_object($arg)) ? get_class($arg) : gettype($arg);
                }, $v['args']));
            }

            $trace .= '#' . ($k) . ' ';
            if (isset($v['file'])) {
                $trace .= $v['file'] . '(' . $v['line'] . '): ';
            }

            if (isset($v['class'])) {
                $trace .= $v['class'] . '->';
            }

            $trace .= $v['function'] . '(' . $args .')' . "\n";
        }

        return $trace;
    }
}
