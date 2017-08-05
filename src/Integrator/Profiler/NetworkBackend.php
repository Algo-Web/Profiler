<?php
namespace algoweb\Profiler\Integrator\Profiler;

class NetworkBackend implements Backend
{
    /**
     * Old v1 type profile format.
     *
     * @var string
     */
    const TYPE_PROFILE = 'profile';
    /**
     * v2 type traces
     */
    const TYPE_TRACE = 'trace';

    private $socketFile;
    private $udp;

    public function __construct($socketFile = "unix:///var/run/algoweb/algowebd.sock", $udp = "127.0.0.1:8135")
    {
        $this->socketFile = $socketFile;
        $this->udp = $udp;
    }

    /**
     * To avoid user apps messing up socket errors that Algoweb can produce
     * when the daemon is not reachable, this error handler is used
     * wrapped around daemons to guard user apps from erroring.
     */
    public static function ignoreErrorsHandler($errno, $errstr, $errfile, $errline)
    {
        // ignore all errors!
    }

    public function socketStore(array $trace)
    {
        if (!function_exists('json_encode')) {
            \algoweb\Profiler\Integrator\Profiler::log(1, "ext/json must be installed and activated to use Agloweb.");
            return;
        }

        set_error_handler(array(__CLASS__, "ignoreErrorsHandler"));
        $fp = stream_socket_client($this->socketFile);

        if ($fp == false) {
            \algoweb\Profiler\Integrator\Profiler::log(1, "Cannot connect to socket for storing trace.");
            restore_error_handler();
            return;
        }

        $payload = json_encode(array('type' => self::TYPE_TRACE, 'payload' => $trace));

        $timeout = (int)ini_get('algoweb.timeout');

        // We always enforce a timeout, even when the user configures
        // algoweb.timeout=0 manually
        if (!$timeout) {
            $timeout = 10000;
        }

        if ($trace['keep']) {
            // as a dev trace we collect more data and the developer can be
            // waiting a little longer to make sure the socket gets everything.
            $timeout *= 10;
        }

        stream_set_timeout($fp, 0, $timeout); // 10 milliseconds max

        if (fwrite($fp, $payload) < strlen($payload)) {
            \algoweb\Profiler\Integrator\Profiler::log(1, "Could not write payload to socket.");
        }
        fclose($fp);
        restore_error_handler();
        \algoweb\Profiler\Integrator\Profiler::log(3, "Sent trace to socket.");
    }

    public function udpStore(array $trace)
    {
        if (!function_exists('json_encode')) {
            \algoweb\Profiler\Integrator\Profiler::log(1, "ext/json must be installed and activated to use Algoweb.");
            return;
        }

        set_error_handler(array(__CLASS__, "ignoreErrorsHandler"));
        $fp = stream_socket_client("udp://" . $this->udp);

        if ($fp == false) {
            \algoweb\Profiler\Integrator\Profiler::log(1, "Cannot connect to UDP port for storing trace.");
            restore_error_handler();
            return;
        }

        unset($trace['id']);

        $payload = json_encode($trace);
        // Golang is very strict about json types.
        $payload = str_replace('"a":[]', '"a":{}', $payload);

        stream_set_timeout($fp, 0, 200);
        if (fwrite($fp, $payload) < strlen($payload)) {
            \algoweb\Profiler\Integrator\Profiler::log(1, "Could not write payload to UDP port.");
        }
        fclose($fp);
        restore_error_handler();
        \algoweb\Profiler\Integrator\Profiler::log(3, "Sent trace to UDP port.");
    }
}