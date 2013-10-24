<?php

class MageModule_Customerprice_Block_Adminhtml_Catalog_Product_Edit_Action_Attribute_Tab_Attributes 
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes
{
    protected function _prepareForm()
    {
        $this->setFormExcludedFieldList(array('tier_price', 'customer_price', 'gallery', 'media_gallery', 'recurring_profile'));
        Mage::dispatchEvent('adminhtml_catalog_product_form_prepare_excluded_field_list', array('object'=>$this));

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('fields', array('legend'=>Mage::helper('catalog')->__('Attributes')));
        $attributes = $this->getAttributes();
        /**
         * Initialize product object as form property
         * for using it in elements generation
         */
        $form->setDataObject(Mage::getModel('catalog/product'));
        $this->_setFieldset($attributes, $fieldset, $this->getFormExcludedFieldList());
        $form->setFieldNameSuffix('attributes');
        $this->setForm($form);
    }
}
