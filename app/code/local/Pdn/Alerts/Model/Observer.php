<?php

Class Pdn_Alerts_Model_Observer
{

    /**
     * Array of all check classes 
     * 
     */
    public static $checks = array('Pdn_Alerts_Model_Stock_Lowstockcheck');

    /**
     * Loop through all check classes and perform checks
     * 
     * @return  void
     */
    public static function checkAlerts()
    {
        Mage::log("Alert checks about to run...");
        
        foreach (self::$checks  as $checkClass)
        {
            $check =  new $checkClass;
            $check->check();
            $check->checkForResolvedAlerts();
        }

        Mage::log("Alert checks complete");
    }


    /**
     * Loop through all check classes and perform purges
     *
     * @return  void
     */
    public static function purgeAlerts()
    {
        Mage::log("Alert purge about to run...");

        foreach (self::$checks as $checkClass)
        {
            $check =  new $checkClass;
            $check->purgeAlerts();
        }

        Mage::log("Alert purge complete");
    }
}