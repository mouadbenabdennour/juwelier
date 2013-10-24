<?php

class MageModule_Customerprice_Model_Catalog_ConfProductAttribute extends Mage_Catalog_Model_Product_Type_Configurable_Attribute
{
    public function addPrice($priceData)
    {
        // CHANGE
        // add the internal flag, so we won't calculate the discount for this
        $data = $this->getPrices(true);
        // CHANGE
        if (is_null($data)) {
            $data = array();
        }
        $data[] = $priceData;
        $this->setPrices($data);
        return $this;
    }

    //CHANGE
    public function getPrices($internal=false)
    {
        $data = parent::getPrices();
        if (is_null($data)) {
            $data = array();
        }
        if ($internal)
            return $data;
        foreach ($data as $k=>$v)
        {
            if ($v['is_percent'] == 0 && $v['pricing_value'])
            {
                $price = $v['pricing_value'];
                if ($price<1)
                    diedump($price);
                $product = null;
                $qty = null;
                if ($product && $product->getCustomerId()) {
                    $customer = $product->getCustomerId();
                } else {
                    $customer = Mage::getSingleton('customer/session')->getCustomerId();
                }
                if (isset($_SESSION['adminhtml_quote']) && isset($_SESSION['adminhtml_quote']['customer_id']))
                    $customer = $_SESSION['adminhtml_quote']['customer_id'];
                $customerObj = Mage::getModel('customer/customer')->load($customer);

                if (Mage::helper('customerprice/productprice')->isPriceAllowed('customer_discount', $customerObj, $product, $qty))
                {
                    if ($customerObj && $customerObj->getDiscount())
                    {
                        $origPrice = $price;
                        $discountedPrice = $price - ($price/100*$customerObj->getDiscount());
                        if ($discountedPrice < $price) {
                            $price = $discountedPrice;
                        }
                    }
                }

                $v['pricing_value'] = $price;
            }
            $data[$k] = $v;
        }
        return $data;
    }
    // CHANGE
}