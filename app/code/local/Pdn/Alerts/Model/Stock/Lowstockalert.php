<?php

/**
 * Low Stock Alert Model
 * 
 */
class Pdn_Alerts_Model_Stock_Lowstockalert extends Pdn_Alerts_Model_Abstract
{

    public function _construct()
    {
        $this->_init('alerts/stock_lowstockalert');
    }
}