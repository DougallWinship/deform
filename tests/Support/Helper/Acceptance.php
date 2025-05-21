<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
    private int $process;

    public function _beforeSuite($settings = [])
    {
        $cmd = 'php -S localhost:8000 tests/_data/public/router.php > tests/_output/server.log 2>&1 & echo $!';
        echo "Starting PHP built-in server...\n";
        $output = [];
        exec($cmd, $output);
        $this->process = (int)$output[0];
        sleep(1);
    }

    public function _afterSuite()
    {
        if (!empty($this->process)) {
            echo "Stopping PHP built-in server (PID {$this->process})...\n";
            exec("kill {$this->process}");
        }
    }

    public function assertArrayContainsSubset($subset, $array) {
        foreach ($subset as $key=>$value) {
            $this->assertArrayHasKey($key, $array);
            $this->assertSame($value, $array[$key]);
        }
    }
}
