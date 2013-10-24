<?php
class MageModule_Customerprice_Model_Catalog_Resource_Eav_Mysql4_Product_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
//    /**
//     * Product limitation filters
//     *
//     * Allowed filters
//     *  store_id                int;
//     *  category_id             int;
//     *  category_is_anchor      int;
//     *  visibility              array|int;
//     *  website_ids             array|int;
//     *  store_table             string;
//     *  use_price_index         bool;   join price index table flag
//     *  customer_group_id       int;    required for price; customer group limitation for price
//     *  website_id              int;    required for price; website limitation for price
//     *
//     * @var array
//     */
//    protected $_productLimitationFilters    = array();
//
    public $correctPriceLoad = false;
    protected function _afterLoad()
    {
        if ($this->correctPriceLoad)
        {
            foreach ($this->_items as $k=>$v)
            {
                //$price = $v->getPrice();
                $v = Mage::getModel('catalog/product')->load($v->getId());
                foreach ($v->getTierPrice() as $p)
                {
                    $v->setTierPrice($p['website_price']);
                    $v->setMinimalPrice($p['website_price']);
                }
                $v->getFinalPrice();
                //diedump($v);
                //continue;
                //$v->setPrice($price);
                $this->_items[$k] = $v;
            }
        }
        return $this;
    }

    /**
     * Join Product Price Table
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _productLimitationJoinPrice()
    {
        $this->correctPriceLoad = true;
        return parent::_productLimitationJoinPrice();
    }

    /**
     * Add customer price data to loaded items
     *
     * @return Mage_Catalog_Model_Resource_Eav_My6sql4_Product_Collection
     */
    public function addCustomerPriceData()
    {
        return $this;
    }
}
