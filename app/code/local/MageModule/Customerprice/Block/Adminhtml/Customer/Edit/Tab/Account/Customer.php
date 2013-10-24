<?php
class MageModule_Customerprice_Block_Adminhtml_Customer_Edit_Tab_Account_Customer
    extends MageModule_Customerprice_Block_Adminhtml_Catalog_Product_Edit_Tab_Price_Customer
{
    /**
     * Define customer price template file
     *
     */
    public function __construct()
    {
        $this->setTemplate('customer/edit/account/customer.phtml');
    }

    /**
     * Retrieve current edit product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_customer');
    }

    /**
     * Prepare Customer Price values
     *
     * @return array
     */
    public function getValues()
    {
        $values = array();
        $customer = Mage::registry('current_customer');
        $data = Mage::helper('customerprice/productprice')->getData($customer->getId());

        if (is_array($data)) {
            usort($data, array($this, '_sortCustomerPrices'));
            $values = $data;
        }

        $returnValues = array();

        foreach ($values as &$v) {
            $v['readonly'] = $v['website_id'] == 0 && $this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite();
            $c = $this->getProducts($v['product_id']);
            $v['productname'] = $c['label'];
            if ($c && $c['label'])
            $returnValues[] = $v;
        }

        return $returnValues;
    }

    /**
     * Retrieve allowed products (through inheriting the names are not that good)
     *
     * @param int $customerId  return name by customer id
     * @return array|string
     */
    public function getProducts($customerId = null)
    {
      if(is_null($this->_customers)) {
      $collection = Mage::getResourceModel('catalog/product_collection')
        ->addAttributeToSelect('name');
        foreach ($collection as $item) {
          $this->_customers[$item->getId()]['id'] = $item->getId();
		  $this->_customers[$item->getId()]['label'] = $item->getSku();
		  $this->_customers[$item->getId()]['label'] .= ' / '.$item->getName();
        }
      }

      if (!is_null($customerId)) {
        return isset($this->_customers[$customerId]) ? $this->_customers[$customerId] : null;
      }

      return $this->_customers;
    }

    public function getAttribute()
    {
        // get the attribute from catalog_product eav
        $eavConfig = Mage::getSingleton('eav/config');
        $attributeCode = $this->getElement()->getId();
        $attribute = $eavConfig->getAttribute('catalog_product', $attributeCode);
        return $attribute;
    }
}
