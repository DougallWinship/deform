<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
    public function assertArrayContainsSubset($subset, $array) {
        foreach ($subset as $key=>$value) {
            $this->assertArrayHasKey($key, $array);
            $this->assertSame($value, $array[$key]);
        }
    }
}
