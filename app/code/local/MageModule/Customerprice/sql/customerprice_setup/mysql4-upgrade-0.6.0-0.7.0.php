<?php

$installer = $this;
$this->startSetup();

$this->addAttribute('catalog_product', 'discountable', array(
                        'type'              => 'int',
                        'frontend'          => '',
                        'label'             => 'Discountable',
                        'input'             => 'select',
                        'class'             => '',
                        'source'            => '',
                        'source'            => 'eav/entity_attribute_source_boolean',
                        'global'            => 0,
                        'visible'           => 1,
                        'required'          => 0,
                        'user_defined'      => 0,
                        'default'           => '',
                        'searchable'        => 0,
                        'filterable'        => 0,
                        'comparable'        => 0,
                        'visible_on_front'  => 1,
                        'unique'            => 0,
                        'position'          => 1,
                        'used_in_product_listing'  => true
    )
);


try{
$installer->run("
    ALTER TABLE `{$installer->getTable('catalog_product_entity_customer_price')}` ADD `from` DATE NULL ,
    ADD `to` DATE NULL;
    ALTER TABLE `{$installer->getTable('catalog_product_entity_customer_price')}` DROP INDEX `UNQ_CATALOG_PRODUCT_CUST_PRICE` ,
    ADD UNIQUE `UNQ_CATALOG_PRODUCT_CUST_PRICE` ( `entity_id` , `customer_id` , `qty` , `website_id` , `from` , `to` );
");
}catch(Exception $e)
{
    // nothing
}

$installer->endSetup();
