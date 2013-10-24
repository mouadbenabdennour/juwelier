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

CREATE TABLE {$installer->getTable('catalog_product_index_customer_price')} (
  `entity_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `website_id` smallint(5) unsigned NOT NULL,
  `min_price` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`entity_id`,`customer_id`,`website_id`),
  KEY `FK_CATALOG_PRODUCT_INDEX_CUSTOMER_PRICE_CUSTOMER` (`customer_id`),
  KEY `FK_CATALOG_PRODUCT_INDEX_TIER_PRICE_WEBSITE` (`website_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$installer->getTable('catalog_product_index_price')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `tier_price`,
  ADD KEY `IDX_CUSTOMER` (`customer_id`),
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_bundle_tmp')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_percent` decimal(12,4) DEFAULT NULL AFTER `tier_percent`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `base_tier`,
  ADD `base_customer_price` decimal(12,4) DEFAULT NULL AFTER `customer_price`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_bundle_idx')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_percent` decimal(12,4) DEFAULT NULL AFTER `tier_percent`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `base_tier`,
  ADD `base_customer_price` decimal(12,4) DEFAULT NULL AFTER `customer_price`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_bundle_opt_tmp')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `alt_tier_price`,
  ADD `alt_customer_price` decimal(12,4) DEFAULT NULL AFTER `customer_price`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`,`option_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_bundle_opt_idx')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `alt_tier_price`,
  ADD `alt_customer_price` decimal(12,4) DEFAULT NULL AFTER `customer_price`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`,`option_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_bundle_sel_tmp')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `tier_price`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`,`option_id`,`selection_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_bundle_sel_idx')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `tier_price`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`,`option_id`,`selection_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_final_tmp')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `base_tier`,
  ADD `base_customer_price` decimal(12,4) DEFAULT NULL AFTER `customer_price`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_final_idx')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `base_tier`,
  ADD `base_customer_price` decimal(12,4) DEFAULT NULL AFTER `customer_price`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_tmp')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `tier_price`,
  ADD KEY `IDX_CUSTOMER` (`customer_id`),
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_idx')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `tier_price`,
  ADD KEY `IDX_CUSTOMER` (`customer_id`),
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_opt_agr_tmp')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `tier_price`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`,`option_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_opt_agr_idx')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `tier_price`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`,`option_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_opt_tmp')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `tier_price`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`);

ALTER TABLE {$installer->getTable('catalog_product_index_price_opt_idx')}
  ADD `customer_id` int(10) unsigned NOT NULL AFTER `entity_id`,
  ADD `customer_price` decimal(12,4) DEFAULT NULL AFTER `tier_price`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`entity_id`,`customer_id`,`customer_group_id`,`website_id`);

ALTER TABLE {$installer->getTable('catalog_product_entity_customer_price')}
  ADD CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_CUSTOMER_PRICE_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CATALOG_PRODUCT_CUSTOMER_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CATALOG_PROD_ENTITY_CUSTOMER_PRICE_PROD_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE {$installer->getTable('catalog_product_index_customer_price')}
  ADD CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_CUSTOMER_PRICE_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_CUSTOMER_PRICE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CATALOG_PRODUCT_INDEX_CUSTOMER_PRICE_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO {$installer->getTable('customer_entity')} (`entity_id`, `is_active`) VALUES(0, 0);
");
}

$installer->endSetup();

$installer->installEntities();
