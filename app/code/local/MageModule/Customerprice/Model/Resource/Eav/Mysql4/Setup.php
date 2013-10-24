<?php
class MageModule_Customerprice_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities()
    {
        return array(
            'catalog_product' => array(
                'entity_model'      => 'catalog/product',
                'attribute_model'   => 'catalog/resource_eav_attribute',
                'table'             => 'catalog/product',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',
                'attributes'        => array(
                    'customer_price' => array(
                        'group'             => 'Prices',
                        'type'              => 'decimal',
                        'backend'           => 'customerprice/catalog_product_attribute_backend_customerprice',
                        'label'             => 'Customer Price',
                        'input'             => 'text',
                        'class'             => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => false,
                        'default'           => '',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
                        'used_for_price_rules' => false,
                        'unique'            => false,
                        'apply_to'          => 'simple,configurable,virtual',
                    ),
                )
            ),
        );
    }
}
