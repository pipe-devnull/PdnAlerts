<?php

/**
 * Low Stock Alert Unit tests
 * 
 * 
 */
Class Pdn_Tests_Unit_LowStockTest extends AbstractTest
{

    /**
     * A lot of tests required the same base mock object so consolidating
     * code in a 'builder' function
     * 
     * @return Pdn_Alerts_Model_Stock_Lowstockcheck PHPUnit Mock Object
     */
    protected function buildBaseLowStockCheckMockObject($lowStockThreshold = 10, $purgeNotificationsThreshold = 100, $resolvedNotifySetting  = false)
    {
         $mockObject = $this->getMockBuilder('Pdn_Alerts_Model_Stock_Lowstockcheck')
                       ->setMethods(array(
                            'getLowStockThreshold',
                            'getPurgeNotificationsThreshold',
                            'getResolvedNotifySetting',
                            'getProductSku',
                            'getWebsiteName',
                            'getLowStockResolvedAlertsList',
                            'getLowStockListExcludingNotified',
                            'getAlertById',
                            'createAlert',
                        ))
                        ->getMock();


        $mockObject->expects($this->any())
                   ->method('getWebsiteName')
                   ->willReturn('Test Website');


        return $mockObject;
    }

    public function buildBaseAlertMockObject($entityId = null, $websiteId = 2, $productId = 1234, $stockId = 1, $message = "TEST", $createdAt = '2016-01-01 10:10:10')
    {
        $alert = $this->getMockBuilder('Pdn_Alerts_Model_Abstract')
                              ->setMethods(array(
                                    'sendAlert',
                                    'delete'
                                ))
                              ->getMock();

        $alert->expects($this->any())
                      ->method('sendAlert')
                      ->willReturn(null);

        $alert->expects($this->any())
                      ->method('delete')
                      ->willReturn(null);

        //$alert->setId($entityId);
        $alert->setProductId($productId);
        $alert->setStockId($stockId);
        $alert->setWebsiteId($websiteId);
        $alert->setNotificationMessage($message);
        $alert->setCreatedAt($createdAt);

        return $alert;
    }



    /**
     * Test that an alert is triggered when a stock item
     * falls below the set threshold
     * 
     */
    public function testLowStockCheckCreatesAnAlert()
    {
        // Assemble
        $mockObject = $this->buildBaseLowStockCheckMockObject();

        $mockObject->expects($this->any())
                   ->method('getLowStockThreshold')
                   ->willReturn(10);

        $mockObject->expects($this->any())
                   ->method('getLowStockListExcludingNotified')
                   ->willReturn(array(array('product_id' => '1234','website_id' => '2','stock_id' => '1', 'qty' =>8, 'stock_status' => '1', 'entity_id' => null)));

        $mockObject->expects($this->exactly(1))
                   ->method('createAlert')
                   ->with('1234','1','2','Item TEST1234 Has dropped to 8 on website Test Website, below the low stock threshold level of 10')
                   ->willReturn($this->buildBaseAlertMockObject());

        $mockObject->expects($this->any())
                   ->method('getProductSku')
                   ->willReturn("TEST1234");

        $mockObject->expects($this->any())
                   ->method('getResolvedNotifySetting')
                   ->willReturn(false);

        // Action & assert ( No return value expected)
        $mockObject->check();
    }


    /**
     * Test that an alert is triggered when a stock item
     * falls below the set threshold
     * 
     */
    public function testLowStockCheckNoAlertsToProcess()
    {
        // Assemble
        $mockObject = $this->buildBaseLowStockCheckMockObject();

        $mockObject->expects($this->any())
                   ->method('getLowStockThreshold')
                   ->willReturn(10);

        // Returns an empty result set
        $mockObject->expects($this->any())
                   ->method('getLowStockListExcludingNotified')
                   ->willReturn(array());

        $mockObject->expects($this->never())
                   ->method('createAlert');

        // Action & assert ( No return value expected)
        $mockObject->check();
    }


    /**
     * Test that an alert is triggered when a stock item
     * falls below the set threshold
     * 
     */
    public function testLowStockCheckDatabaseExceptionThrown()
    {
        // Assemble
        $mockObject = $this->buildBaseLowStockCheckMockObject();

        $mockObject->expects($this->any())
                   ->method('getLowStockThreshold')
                   ->willReturn(10);

        // Throws an exeption - should be caught and end gracefully
        $mockObject->expects($this->any())
                   ->method('getLowStockListExcludingNotified')
                   ->will($this->throwException(new Exception("No Database!")));

        $mockObject->expects($this->never())
                   ->method('createAlert');

        // Action & assert ( No return value expected)
        $mockObject->check();
    }


    /**
     * Test that code exits if a lowStock Threshold is not configured
     * 
     */
    public function testLowStockUnconfiguredThreshold()
    {
        // Assemble
        $mockObject = $this->buildBaseLowStockCheckMockObject();

        $mockObject->expects($this->any())
                   ->method('getLowStockThreshold')
                   ->willReturn(null);

        // Assert that the method exits early and does not attempt any processing
        $mockObject->expects($this->never())
                   ->method('getLowStockListExcludingNotified');

        // Action & assert ( No return value expected)
        $mockObject->check();
    }

    /**
     * Test that code exits if a lowStock Threshold is not configured
     * 
     */
    public function testLowStockUnconfiguredPurge()
    {
        // Assemble
        $mockObject = $this->buildBaseLowStockCheckMockObject();

        $mockObject->expects($this->any())
                   ->method('getPurgeNotificationsThreshold')
                   ->willReturn(null);

        // Assert that the method exits early and does not attempt any processing
        $mockObject->expects($this->never())
                   ->method('getLowStockListExcludingNotified');

        // Action & assert ( No return value expected)
        $mockObject->purgeAlerts();
    }


    /**
     * Chekc that an alert is cleared correctly
     * 
     * @todo  build mock fully so that we can assert delete is called
     */
    public function testCheckForResolvedAlerts()
    {
        // Assemble
         
        $alertOne = $this->buildBaseAlertMockObject(1, '1234',1, 2, 'TEST ALERT 1234', '2016-01-01 10:10:10');
        $alertTwo = $this->buildBaseAlertMockObject(2, '4321',1, 2, 'TEST ALERT 4321', '2016-02-02 10:10:10');

        $mockObject = $this->buildBaseLowStockCheckMockObject();

        $mockObject->expects($this->any())
                   ->method('getLowStockThreshold')
                   ->willReturn(10);

        $mockObject->expects($this->any())
                   ->method('getResolvedNotifySetting')
                   ->willReturn(true);

        $mockObject->expects($this->at(1))
                   ->method('getAlertById')
                   ->willReturn($alertOne);

        $mockObject->expects($this->at(2))
                   ->method('getAlertById')
                   ->willReturn($alertTwo);

        $mockObject->expects($this->at(1))
                   ->method('getProductSku')
                   ->willReturn("TEST1234");

        $mockObject->expects($this->at(2))
                   ->method('getProductSku')
                   ->willReturn("TEST4321");

        $mockObject->expects($this->any())
                   ->method('getLowStockResolvedAlertsList')
                   ->willReturn(array(
                        array('p.product_id' => '1234','p.entity_id' => 1),
                        array('p.product_id' => '4321','p.entity_id' => 2)
                    ));

        // Action & assert ( No return value expected)
        $mockObject->checkForResolvedAlerts();
    }


    /**
     * Test that notiicaitons are not sent when configured not to send.
     * 
     */
    public function testCheckForResolvedAlertsNoNotification()
    {
        // Assemble
        $alertOne = $this->buildBaseAlertMockObject(1, '1234',1, 2, 'TEST ALERT 1234', '2016-01-01 10:10:10');

        $mockObject = $this->buildBaseLowStockCheckMockObject();

        $mockObject->expects($this->any())
                   ->method('getLowStockThreshold')
                   ->willReturn(10);

        $mockObject->expects($this->any())
                   ->method('getResolvedNotifySetting')
                   ->willReturn(false);

        $mockObject->expects($this->once())
                   ->method('getAlertById')
                   ->willReturn($alertOne);

        $mockObject->expects($this->never())
                   ->method('getProductSku');

        $mockObject->expects($this->any())
                   ->method('getLowStockResolvedAlertsList')
                   ->willReturn(array(
                        array('p.product_id' => '1234','p.entity_id' => 1)
                    ));

        // Action & assert ( No return value expected)
        $mockObject->checkForResolvedAlerts();
    }
}