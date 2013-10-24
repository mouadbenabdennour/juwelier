<?php
class MageModule_Customerprice_Model_Catalog_Price_Observer
{
    public function __construct()
    {
    }
    /**
     * Applies the special price percentage discount
     * @param   Varien_Event_Observer $observer
     * @return  Xyz_Catalog_Model_Price_Observer
     */
    public function apply_customerprice($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $qty = $observer->getEvent()->getQty();
        if ($product->getCustomerId()) {
                $customer = $product->getCustomerId();
        } else {
            $customer = Mage::getSingleton('customer/session')->getCustomerId();
        }
        if (isset($_SESSION['adminhtml_quote']) && isset($_SESSION['adminhtml_quote']['customer_id']))
            $customer = $_SESSION['adminhtml_quote']['customer_id'];

        $customerObj = Mage::getModel('customer/customer')->load($customer);
        $customerPrices = $product->getData('customer_price');

        $price = $product->getData('final_price');
        // process customerprice only for simple products
        if ($product->getSuperProduct() && $product->getSuperProduct()->isConfigurable()) {
            // nothing
		}
		else 
		{
			if (!$qty)
			{
				$qty = 1;
			}

			if ($customer != "")
			{
				// get the lowest customer price
				$price = $product->getTierPrice($qty);

				$product->setFinalPrice($price); // set the product final price
				$product->setPrice($price); // set the product price (else it will be the normal one.. magento also already applies taxes to this price later.. just call $product->getFinalPrice() before getPrice() will
			}
		}

		// be outputted
		$product->setMyFinalPrice($price); // set the product final price
		return $this;
    }
}
