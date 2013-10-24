<?php

class MageModule_Customerprice_Model_Customerattribute extends Mage_Customer_Model_Attribute
{
    public function getDefaultValue()
    {
        if ($this->getAttributeCode() == 'customer_verify')
        {
            return Mage::getStoreConfig('customer_price/customer/verified_default');
        }
        return parent::getDefaultValue();
    }
}