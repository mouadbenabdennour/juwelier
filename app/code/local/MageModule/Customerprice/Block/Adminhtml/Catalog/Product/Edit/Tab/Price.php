<?php
class MageModule_Customerprice_Block_Adminhtml_Catalog_Product_Edit_Tab_Price 
  extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price
{
    protected function _prepareForm()
    {
        $product = Mage::registry('product');

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('tiered_price', array('legend'=>Mage::helper('catalog')->__('Tier Pricing')));

        $fieldset->addField('default_price', 'label', array(
                'label'=> Mage::helper('catalog')->__('Default Price'),
                'title'=> Mage::helper('catalog')->__('Default Price'),
                'name'=>'default_price',
                'bold'=>true,
                'value'=>$product->getPrice()
        ));

        $fieldset->addField('tier_price', 'text', array(
                'name'=>'tier_price',
                'class'=>'requried-entry',
                'value'=>$product->getData('tier_price')
        ));

        $form->getElement('tier_price')->setRenderer(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
        );

        $fieldset_cust = $form->addFieldset('customer_pricing', array('legend'=>Mage::helper('catalog')->__('Customer Pricing')));

        $fieldset_cust->addField('customer_price', 'text', array(
                'name'=>'customer_price',
                'class'=>'requried-entry',
                'value'=>$product->getData('customer_price')
        ));

        $form->getElement('customer_price')->setRenderer(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_customer')
        );

        $this->setForm($form);
    }
}
