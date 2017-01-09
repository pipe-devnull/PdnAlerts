<?php

/**
 * Low Stock Alert Unit tests
 * 
 * 
 */
Class Pdn_Tests_Unit_LowStockTest extends PHPUnit_Framework_TestCase
{
    /**
     * Set up before each test.
     *
     * @return void
     */
    public function setUp()
    {
        Mage::app();
    }

    /**
     * 
     * 
     * @return  void
     */
    public function testLowStockCheckCreatesAnAlert()
    {
        // Assemble
        $mockObject = $this->getMockBuilder('Pdn_Alerts_Model_Stock_Lowstockcheck')
                       ->setMethods(array(
                            'getLowStockThreshold',
                            'getPurgeNotificationsThreshold',
                            'getResolvedNotifySetting',
                            'getProductName',
                            'getLowStockResolvedAlertsList',
                            'getLowStockListExcludingNotified',
                            'sendEmailAlert',
                        ))
                        ->getMock();

        $mockObject->expects()
               ->method('getLowStockListExcludingNotified')
               ->willReturn(array(array('1234','2','1','8','1','null')));

        $mockObject->expects()
               ->method('getProductName')
               ->willReturn('Test1234');

        $mockObject->expects($this->exactly(1))
               ->method('sendEmailAlert')
               ->with('1','1','2','"Item Test1234 Has dropped to 8 on website 2, below the low stock threshold level of 10')
               ->willReturn(null);

        // Action & assert ( No return value expected)
        $lowStockCheck->check();
    }
}
    