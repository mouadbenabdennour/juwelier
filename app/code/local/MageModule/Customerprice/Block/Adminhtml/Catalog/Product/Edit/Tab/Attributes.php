<?php
class MageModule_Customerprice_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes 
  extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
    protected function _prepareForm()
    {
		// old code wich is shitty
		if (0)
		{
        if ($group = $this->getGroup()) {
            $form = new Varien_Data_Form();
            /**
             * Initialize product object as form property
             * for using it in elements generation
             */
            $form->setDataObject(Mage::registry('product'));

            $fieldset = $form->addFieldset('group_fields'.$group->getId(),
                array(
                    'legend'=>Mage::helper('catalog')->__($group->getAttributeGroupName()),
                    'class'=>'fieldset-wide',
            ));

            $attributes = $this->getGroupAttributes();

            $this->_setFieldset($attributes, $fieldset, array('gallery'));

            if ($urlKey = $form->getElement('url_key')) {
                $urlKey->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_form_renderer_attribute_urlkey')
                );
            }

            if ($tierPrice = $form->getElement('tier_price')) {
                $tierPrice->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
                );
            }

            if ($customerPrice = $form->getElement('customer_price')) {
                $customerPrice->setRenderer(
                    $this->getLayout()->createBlock('customerprice_adminhtml/catalog_product_edit_tab_price_customer')
                );
            }

            if ($recurringProfile = $form->getElement('recurring_profile')) {
                $recurringProfile->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_recurring')
                );
            }

            /**
             * Add new attribute button if not image tab
             */
            if (!$form->getElement('media_gallery')
                 && Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/attributes')) {
                $headerBar = $this->getLayout()->createBlock(
                    'adminhtml/catalog_product_edit_tab_attributes_create'
                );

                $headerBar->getConfig()
                    ->setTabId('group_' . $group->getId())
                    ->setGroupId($group->getId())
                    ->setStoreId($form->getDataObject()->getStoreId())
                    ->setAttributeSetId($form->getDataObject()->getAttributeSetId())
                    ->setTypeId($form->getDataObject()->getTypeId())
                    ->setProductId($form->getDataObject()->getId());

                $fieldset->setHeaderBar(
                    $headerBar->toHtml()
                );
            }

            if ($form->getElement('meta_description')) {
                $form->getElement('meta_description')->setOnkeyup('checkMaxLength(this, 255);');
            }

            $values = Mage::registry('product')->getData();
            /**
             * Set attribute default values for new product
             */
            if (!Mage::registry('product')->getId()) {
                foreach ($attributes as $attribute) {
                    if (!isset($values[$attribute->getAttributeCode()])) {
                        $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                    }
                }
            }
            
            // set default discountable for new products
            if (Mage::registry('product')->isObjectNew())
                $values['discountable'] = Mage::getStoreConfig('customer_price/product/discountable_default');

            if (Mage::registry('product')->hasLockedAttributes()) {
                foreach (Mage::registry('product')->getLockedAttributes() as $attribute) {
                    if ($element = $form->getElement($attribute)) {
                        $element->setReadonly(true, true);
                    }
                }
            }

            Mage::dispatchEvent('adminhtml_catalog_product_edit_prepare_form', array('form'=>$form));

            $form->addValues($values);
            $form->setFieldNameSuffix('product');
            $this->setForm($form);
        }
		}

		// new code wich is pretty
		parent::_prepareForm();
		// yep thats all

    }
}
