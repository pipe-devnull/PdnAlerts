<?php

/**
 * Alert collection
 * 
 * 
 */
class Pdn_Alerts_Model_Mysql4_Stock_Lowstockalert_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('alerts/stock_lowstockalert');
    }
}
