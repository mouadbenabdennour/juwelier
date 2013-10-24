<?php

/**
 * @var Mage_Customer_Model_Entity_Setup $installer
 */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('customer', 'price_adjustment', array(
	'label'			=> 'Kundenrabatt',
	'type'			=> 'decimal',
	'input'			=> 'text',
	'default'		=> '0',
	'required'		=> '0'
));

$installer->addAttribute('customer', 'price_adjustment_type', array(
	'label'			=> 'Art des Rabatts',
	'type'			=> 'varchar',
	'input'			=> 'select',
	'source'		=> 'kundenpreise/entity_attribute_source_type',
	'default'		=> Webkochshop_Kundenpreise_Model_Entity_Attribute_Source_Type::TYPE_FIX
));

$installer->endSetup();