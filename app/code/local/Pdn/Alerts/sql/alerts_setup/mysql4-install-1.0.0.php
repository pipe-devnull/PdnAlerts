<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
  DROP TABLE IF EXISTS {$this->getTable('pdn_lowstock_alert')};

  CREATE TABLE  {$this->getTable('pdn_lowstock_alert')} (
    `entity_id` int(7) unsigned NOT NULL auto_increment,
    `product_id` int(10) NOT NULL,
    `stock_id` int(7) NOT NULL,
    `website_id` int(7) NOT NULL,
    `notification_message` varchar(255) NOT NULL,
    `created_at` timestamp default CURRENT_TIMESTAMP,
    PRIMARY KEY  (`entity_id`),
    INDEX product (`product_id`),
    INDEX created_idx (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();