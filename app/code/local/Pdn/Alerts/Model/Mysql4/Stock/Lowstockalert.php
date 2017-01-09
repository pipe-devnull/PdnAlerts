<?php

/**
 * Low Stock Alert resource model
 * 
 */
class Pdn_Alerts_Model_Mysql4_Stock_Lowstockalert extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('alerts/stock_lowstockalert', 'entity_id');
    }    
}