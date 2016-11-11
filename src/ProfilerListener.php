<?php
namespace algoweb\Profiler\Listener;
class XHProfTestListener implements \PHPUnit_Framework_TestListener
{
    protected $map = array();
    /**
     * @var array
     */
    protected $runs = array();
    /**
     * @var array
     */
    protected $options = array();
    /**
     * @var integer
     */
    protected $suites = 0;
    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }
    /**
     * An error occurred.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $e
     * @param float                  $time
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }
    /**
     * A failure occurred.
     *
     * @param \PHPUnit_Framework_Test                 $test

     * @param \PHPUnit_Framework_AssertionFailedError $e
     * @param float                                  $time
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }
    /**
     * Incomplete test.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $e
     * @param float                  $time
     */
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }
    /**
     * Skipped test.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $e
     * @param float                  $time
     */
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }
    /**
     * Risky test.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $e
     * @param float                  $time
     */
    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }
    /**
     * A test started.
     *
     * @param \PHPUnit_Framework_Test $test
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        $name = $test->getName();

        if (!isset($this->options['xhprofFlags'])) {
            $flags = XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY;
        } else {
            $flags = 0;
            foreach (explode(',', $this->options['xhprofFlags']) as $flag) {
                $flags += constant($flag);
            }
        }
        xhprof_enable($flags, array(
//            'ignored_functions' => explode(',', $this->options['xhprofIgnore'])
        ));
    }
    /**
     * A test ended.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param float                  $time
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        $data         = xhprof_disable();
        $name = $test->getName();

        $path = dirname(__FILE__) . "/res/";
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $name);
        $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
        $filename = md5($filename);
	$this->map[$filename] = $name;
        file_put_contents($path . $filename.".xhprof" ,json_encode ($data));
    }
    /**
     * A test suite started.
     *
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->suites++;
    }
    /**
     * A test suite ended.
     *
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->suites--;
if($this->suites == 0){
        $filename =dirname(__FILE__) . "/" . time().".tar";
	$a = new \PharData($filename);
	$files = scandir(dirname(__FILE__) . "/res/");
	foreach($files as $file){
	if($file == "." || $file == ".."){continue;}
                $a->addFile( dirname(__FILE__) . "/res/" . $file , "/" . $file);
		echo(dirname(__FILE__) . "/res/" . $file . "\r\n");
                unlink( dirname(__FILE__) . "/res/" . $file );
        }
        $a->addFromString("/map.json",json_encode($this->map));
        $a->compress(\Phar::GZ);
	unlink($filename);
    }

}
}

