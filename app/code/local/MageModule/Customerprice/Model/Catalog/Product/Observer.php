<?php
 class MageModule_Customerprice_Model_Catalog_Product_Observer extends Mage_Core_Model_Abstract
 {
    /**
     * Called in adminhtml and frontend area
     *
     * @param Varien_Event_Observer $observer
     * @return null
     */
    public function productSaveBefore($observer)
    {
        return;
        
    }
}
