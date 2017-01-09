<?php

abstract class AbstractTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        require_once(MAGENTO_ROOT . '/app/Mage.php' );
        Mage::app();
    }

    public function tearDown()
    {

    }

    protected function exec($command)
    {
        $startTime = microtime(true);
        $output = null;
        $returnValue = null;
        exec($command, $output, $returnValue);
        // var_dump($output, $returnValue);
        $duration = microtime(true) - $startTime;
    }
}