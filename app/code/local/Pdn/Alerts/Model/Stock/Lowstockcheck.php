<?php

/**
 * Low Stock Alert Check
 * 
 * This class contins all logic for triggering low stock alerts.
 * 
 */
class Pdn_Alerts_Model_Stock_Lowstockcheck extends Pdn_Alerts_Model_Check_Abstract implements Pdn_Alerts_Model_Check_Interface
{

    /**
     * Main check routine for alert logic
     * 
     * This method will raise alerts for low stock levels and trigger notifications
     * 
     */
    public function check()
    {
        if(null === ($lowStockThreshold = $this->getLowStockThreshold()))
        {
            Mage::log("Low Stock threshold has not been configured. Configuration must be set before any checks can run");
            return;
        }

        try
        {
            $lowStockList = $this->getLowStockListExcludingNotified($lowStockThreshold);

            foreach($lowStockList as $lowStock)
            {
                $productSku  = $this->getProductSku($lowStock['product_id']);
                $websiteName = $this->getWebsiteName($lowStock['website_id']);

                $message = "Item " . $productSku . " Has dropped to " . $lowStock['qty'] . " on website " . 
                            $websiteName . ", below the low stock threshold level of " . $lowStockThreshold;

                $alert = $this->createAlert($lowStock['product_id'], $lowStock['stock_id'], $lowStock['website_id'], $message);

                $alert->sendAlert("Low Stock Alert: " . $productSku, $message);

                Mage::log("Stock notification raised and sent, message:" . $message);
            }
        }
        catch (Exception $e)
        {
            Mage::logException($e);
        }
    }


    /**
     * Create a low stock notification
     * 
     * @return  Pdn_Alerts_Model_Stock_Lowstockalert description
     */
    public function createAlert($productId, $stockId, $websiteId, $message)
    {
        $lowStockAlert = Mage::getModel('alerts/stock_lowstockalert');
        $lowStockAlert->setProductId($productId);
        $lowStockAlert->setStockId($stockId);
        $lowStockAlert->setWebsiteId($websiteId);
        $lowStockAlert->setNotificationMessage($message);
        $lowStockAlert->save();

        return $lowStockAlert;
    }


    /**
     * Query DB for products with a newly low stock level
     * 
     * @return array array or arrays (rows)
     */
    public function getLowStockListExcludingNotified($lowStockThreshold)
    {
        try
        {
            $select = $this->getReadDbConnection()->select()
                           ->from(array('c' => 'cataloginventory_stock_status'), array('c.product_id','c.website_id','c.stock_id','c.qty','c.stock_status'))
                           ->joinLeft(array('p' => 'pdn_lowstock_alert'), 'c.product_id=p.product_id', array('p.entity_id'))
                           ->where("c.qty <= ?", $lowStockThreshold)
                           ->where("c.qty > 0")
                           ->where("c.stock_status <> ?", 0)
                           ->where("p.entity_id IS NULL");

            $lowStockList = $this->getReadDbConnection()->fetchAll($select);

            return $lowStockList;
        }
        catch (Exception $e)
        {
            Mage::logException($e);
            return array();
        }
    }


    /**
     * Check if we should be clearing any resolved notifications
     * 
     * @return array array or arrays (rows)
     */
    public function checkForResolvedAlerts()
    {

        if(null === $lowStockThreshold = $this->getLowStockThreshold())
        {
            Mage::log("Low Stock threshold has not been configured. Configuration must be set before any checks can run");
            return array();
        }

        try
        {
            foreach ($this->getLowStockResolvedAlertsList($lowStockThreshold) as $resolvedAlert)
            {
                var_dump($resolvedAlert);
                $lowStockAlert = $this->getAlertById($resolvedAlert['entity_id']);

                if ($this->getResolvedNotifySetting())
                {
                    $productSku = $this->getProductSku($resolvedAlert['product_id']);
                    $lowStockAlert->sendAlert($alert, "Low Stock Alert resolved for product " . $productSku);
                    $lowStockAlert->delete();
                }
                else
                {
                    $lowStockAlert->delete();
                }
            
            }
        }
        catch (Exception $e)
        {
            Mage::logException($e);
        }
    }


    /**
     * Load an alert.
     * 
     */
    public function getAlertById($alertId)
    {
        return Mage::getModel('alerts/stock_lowstockalert')->load($alertId);
    }


    /**
     * Query DB for products with a newly low stock level
     * 
     * @return array array or arrays (rows)
     */
    public function getLowStockResolvedAlertsList($lowStockThreshold)
    {
        $select = $this->getReadDbConnection()->select()
                       ->from(array('c' => 'cataloginventory_stock_status'), array('c.product_id'))
                       ->join(array('p' => 'pdn_lowstock_alert'), 'c.product_id=p.product_id', array('p.entity_id'))
                       ->where("c.qty > ?", $lowStockThreshold);

        $list = $this->getReadDbConnection()->fetchAll($select);

        return $list;
    }


    /**
     * Get product name from the product ID
     * 
     */
    public function getProductSku($productId)
    {
        return Mage::getModel('catalog/product')->load($productId)->getSku();
    }


    /**
     * Get website name
     * 
     */
    public function getWebsiteName($websiteId)
    {
        return Mage::getModel('core/website')->load($websiteId)->getName();
    }


    /**
     * Purge old stock notifications
     * 
     * @return  void
     */
    public function purgeAlerts()
    {
        if(null === ($purgeThereshold = $this->getPurgeNotificationsThreshold()))
        {
            Mage::log("Purge threshold has not been configured. Configuration must be set before any checks can run");
            return;
        }

        Mage::log("Purge - DELETING - " . $purgeThereshold);

        $this->getWriteDbConnection()->delete('pdn_lowstock_alert','UNIX_TIMESTAMP(created_at) < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL ' . $purgeThereshold . ' DAY))');
    }


    /**
     * Get the low stock threshold from config
     * 
     * Path: stock/thresholds/lowstock
     * 
     * @return  int 
     * 
     */
    public function getLowStockThreshold()
    {
        return Mage::getStoreConfig('stock/thresholds/lowstock');
    }


    /**
     * Get the purge setting from config
     * 
     * Path: stock/thresholds/purge
     * 
     * @return  int 
     * 
     */
    public function getPurgeNotificationsThreshold()
    {
        return Mage::getStoreConfig('stock/thresholds/purge');
    }

    /**
     * Get the resolved notify setting from config
     * 
     * Path: stock/thresholds/notifyresolved
     * 
     * @return  int 
     * 
     */
    public function getResolvedNotifySetting()
    {
        return Mage::getStoreConfig('stock/thresholds/notifyresolved');
    }
    

}