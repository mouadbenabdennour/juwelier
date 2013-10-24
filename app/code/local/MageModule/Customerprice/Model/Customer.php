<?php
class MageModule_Customerprice_Model_Customer extends Mage_Customer_Model_Customer
{
    public function loadByEmail($customerEmail)
    {
        if (strpos($customerEmail, '@'))
            $this->_getResource()->loadByEmail($this, $customerEmail);
        else
            $this->_getResource()->loadByCustomerId($this, $customerEmail);
        return $this;
    }
}
