<?php

$installer = $this;

$installer->startSetup();

if (!$installer->tableExists($installer->getTable('catalog_product_entity_customer_price'))) {

$installer->run("

CREATE TABLE {$installer->getTable('catalog_product_entity_customer_price')} (
  `value_id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `customer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `qty` decimal(12,4) NOT NULL DEFAULT '1.0000',
  `value` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `website_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`value_id`),
  UNIQUE KEY `UNQ_CATALOG_PRODUCT_CUST_PRICE` (`entity_id`,`customer_id`,`qty`,`website_id`),
  KEY `FK_CATALOG_PRODUCT_ENTITY_CUSTOMER_PRICE_CUSTOMER_ENTITY` (`customer_id`),
  KEY `FK_CATALOG_PRODUCT_ENTITY_CUSTOMER_PRICE_PRODUCT_ENTITY` (`entity_id`),
  KEY `FK_CATALOG_PRODUCT_ENTITY_CUSTOMER_PRICE_WEBSITE` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");
}

$installer->endSetup();

$installer->installEntities();
