<?php

// 0.2

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


// 0.3

$this->startSetup();

$this->addAttribute('customer', 'customer_id', array(
        'type' => 'varchar',
        'label'    => 'Customer Id',
        'visible'  => true,
        'required' => false,
        'position'     => 47,
        'user_defined' => true,
    ));

$this->endSetup();

if (version_compare(Mage::getVersion(), '1.4.0.0.8', '>='))
{

    $store = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);

    /* @var $eavConfig Mage_Eav_Model_Config */
    $eavConfig = Mage::getSingleton('eav/config');

    // update customer system attributes data
    $attributes = array(
        'customer_id'        => array(
            'is_user_defined'   => 1,
            'is_system'         => 0,
            'is_visible'        => 1,
            'sort_order'        => 47,
            'is_required'       => 0,
            'adminhtml_only'    => 1,
        ),
    );

    foreach ($attributes as $attributeCode => $data) {
        $attribute = $eavConfig->getAttribute('customer', $attributeCode);
        // $attribute->setWebsite($store->getWebsite());
        $attribute->addData($data);
        if (false === ($data['is_system'] == 1 && $data['is_visible'] == 0)) {
            $usedInForms = array(
                'customer_account_create',
                'customer_account_edit',
            );
            if (!empty($data['adminhtml_only'])) {
                $usedInForms = array('adminhtml_customer');
            } else {
                $usedInForms[] = 'adminhtml_customer';
            }
            if (!empty($data['admin_checkout'])) {
                $usedInForms[] = 'adminhtml_checkout';
            }

            $attribute->setData('used_in_forms', $usedInForms);
        }
        $attribute->save();
    }
}




// 0.6

$this->startSetup();

$this->addAttribute('customer', 'discount', array(
        'type' => 'decimal',
        'label' => 'Customer Discount (%)',
        'visible'  => true,
        'required' => false,
        'user_defined' => true,
        'default'=>0,
    ));
$this->addAttribute('customer', 'customer_verify', array(
        'type' => 'int',
        'input' => 'boolean',
        'label' => 'Verified for discounts',
        'visible'  => true,
        'required' => false,
        'user_defined' => true,
        'default'=>0,
    ));


$store = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);

/* @var $eavConfig Mage_Eav_Model_Config */
$eavConfig = Mage::getSingleton('eav/config');

// update customer system attributes data
$attributes = array(
    'discount'        => array(
        'is_user_defined'   => 1,
        'is_system'         => 0,
        'is_visible'        => 1,
        'is_required'       => 0,
        'adminhtml_only'    => 1,
    ),
    'customer_verify'        => array(
        'is_user_defined'   => 1,
        'is_system'         => 0,
        'is_visible'        => 1,
        'is_required'       => 0,
        'adminhtml_only'    => 1,
    ),
);

foreach ($attributes as $attributeCode => $data) {
    $attribute = $eavConfig->getAttribute('customer', $attributeCode);
    // $attribute->setWebsite($store->getWebsite());
    $attribute->addData($data);
    if (false === ($data['is_system'] == 1 && $data['is_visible'] == 0)) {
        $usedInForms = array(
            'customer_account_create',
            'customer_account_edit',
        );
        if (!empty($data['adminhtml_only'])) {
            $usedInForms = array('adminhtml_customer');
        } else {
            $usedInForms[] = 'adminhtml_customer';
        }
        if (!empty($data['admin_checkout'])) {
            $usedInForms[] = 'adminhtml_checkout';
        }

        $attribute->setData('used_in_forms', $usedInForms);
    }
    $attribute->save();
}

$this->endSetup();

