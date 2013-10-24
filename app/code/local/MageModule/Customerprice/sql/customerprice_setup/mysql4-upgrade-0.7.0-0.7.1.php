<?php
#update to 0.7.1
#
# Todo:
#	-	check if frontend_label works on all versions or just 1.7

$installer = $this;
$this->startSetup();

#change attributes
$entityTypeId = $installer->getEntityTypeId('catalog_product');
$attributeId = $this->getAttribute($entityTypeId, 'discountable', 'attribute_id');
$installer->updateAttribute($entityTypeId, $attributeId, array(
    'frontend_label' => 'Customer Price Enabled'
));

$entityTypeId = $installer->getEntityTypeId('customer');
$attributeId = $this->getAttribute($entityTypeId, 'customer_verify', 'attribute_id');
$installer->updateAttribute($entityTypeId, $attributeId, array(
    'frontend_label' => 'Customer Price - Verified for discount'
));

$entityTypeId = $installer->getEntityTypeId('customer');
$attributeId = $this->getAttribute($entityTypeId, 'discount', 'attribute_id');
$installer->updateAttribute($entityTypeId, $attributeId, array(
    'frontend_label' => 'Customer Price - Discount (%)'
));

/*
$entityTypeId = $installer->getEntityTypeId('customer');
$attributeId = $this->getAttribute($entityTypeId, 'customer_verify');
file_put_contents("/tmp/update", "entityTypeId: " . $entityTypeId . "\nattribute_idlast: " . $attributeId . "\n", FILE_APPEND);
print_r($attributeId);

$output = "";

file_put_contents("/tmp/update", var_export($attributeId, true), FILE_APPEND);

$attributeId = $this->getAttribute($entityTypeId, 'customer_verify', 'attributeId');
$installer->updateAttribute($entityTypeId, $attributeId, array(
    'frontend_label' => 'Customer Price - Verified'
));
$attributeId = $this->getAttribute($entityTypeId, 'customer_verify');
print_r($attributeId);
 */

$installer->endSetup();
