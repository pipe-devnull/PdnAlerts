<?php

/**
 * Alert check interface
 * 
 * 
 * 
 */
Interface Pdn_Alerts_Model_Check_Interface
{

    /**
     * Main method that the module cron job will call for each
     * type of notification.  Implement this to see if you need
     * to raise any new notifications.
     * 
     * @return void
     * 
     */ 
    public function check();


    /**
     * Check to see if any active notifcations have been resolved.
     * 
     * @return  void
     */
    public function checkForResolvedAlerts();


    /**
     * All notifications should clean up after themselves.  This
     * method should clear down stale notifications
     * 
     * @return void
     */
    public function purgeAlerts();



}