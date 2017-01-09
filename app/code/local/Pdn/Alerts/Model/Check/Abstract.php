<?php

/**
 * Abstract check class
 * 
 * Common methods for use by all check classes
 * 
 */
Class Pdn_Alerts_Model_Check_Abstract
{


    /**
     * Get a read conenction to the DB for performing checks
     * 
     * 
     */ 
    public function getReadDbConnection()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }


     /**
     * Get a write conenction to the DB
     * 
     * 
     */ 
    public function getWriteDbConnection()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_write');
    }
}