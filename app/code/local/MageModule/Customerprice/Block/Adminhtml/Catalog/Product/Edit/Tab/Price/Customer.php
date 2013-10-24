<?php
class MageModule_Customerprice_Block_Adminhtml_Catalog_Product_Edit_Tab_Price_Customer
    extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Form element instance
     *
     * @var Varien_Data_Form_Element
     */
    protected $_element;

    /**
     * Customers cache
     *
     * @var array
     */
    protected $_customers;

    /**
     * Websites cache
     *
     * @var array
     */
    protected $_websites;

    /**
     * Define customer price template file
     *
     */
    public function __construct()
    {
        $this->setTemplate('catalog/product/edit/price/customer.phtml');
    }

    /**
     * Retrieve current edit product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Render HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Set form element instance
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Customer
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Retrieve form element instance
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Customer
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Prepare Customer Price values
     *
     * @return array
     */
    public function getValues()
    {
        $values = array();
        $data   = $this->getElement()->getValue();

        if (is_array($data)) {
            usort($data, array($this, '_sortCustomerPrices'));
            $values = $data;
        }

        $returnValues = array();
        foreach ($values as &$v) {
            $v['readonly'] = $v['website_id'] == 0 && $this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite();
            $c = $this->getCustomers($v['customer']);
            $v['customername'] = $c['label'];
            if ($c)
                $returnValues[] = $v;
        }

        return $returnValues;
    }

    /**
     * Sort customer price values callback method
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortCustomerPrices($a, $b)
    {
        if ($a['website_id'] != $b['website_id']) {
            return $a['website_id'] < $b['website_id'] ? -1 : 1;
        }
        if ($a['customer'] != $b['customer']) {
            return $a['customer'] < $b['customer'] ? -1 : 1;
        }
        if ($a['price_qty'] != $b['price_qty']) {
            return $a['price_qty'] < $b['price_qty'] ? -1 : 1;
        }

        return 0;
    }

    /**
     * Retrieve allowed customers
     *
     * @param int $customerId  return name by customer id
     * @return array|string
     */



    protected function formatCustomerId($customerId)
    {
        if (!Mage::getStoreConfig('customer/customer_id/auto_increment'))
            return $customerId;
        if (!Mage::getStoreConfig('customer/customer_id/template_force_apply'))
            return $customerId;

        if (!$customerId)
            return $customerId;

        // no number inside customer_id
        if (!preg_match('/[1-9]+[0-9]*/', $customerId, $result))
            return $customerId;
        $number = $result[0];

        $template = Mage::getStoreConfig('customer/customer_id/id_template');

        $p = sscanf($customerId, $template);
        if (!$p[0])
        {
            return sprintf($template, $number);
        }
        return $customerId;
    }



    public function getCustomers($customerId = null)
    {
      if(is_null($this->_customers)) {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('customer_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left');

        foreach ($collection as $item) {
          $this->_customers[$item->getId()]['id'] = $item->getId();
          $label = $item->getName();
          if($item->getBillingPostcode()) {
            $label .= ' / ' . $item->getBillingCity() . ' / ' . $item->getBillingPostcode();
          }
          if($cId = $this->formatCustomerId($item->getCustomerId())) {
           $label .= ' / '. $cId;
          }
          $this->_customers[$item->getId()]['label'] = $label;
        }
      }

      if (!is_null($customerId)) {
        return isset($this->_customers[$customerId]) ? $this->_customers[$customerId] : null;
      }

      return $this->_customers;
    }

    /**
     * Retrieve count of websites
     *
     * @return int
     */
    public function getWebsiteCount()
    {
        return count($this->getWebsites());
    }

    /**
     * Show Website column and switcher for customer price table
     *
     * @return bool
     */
    public function isMultiWebsites()
    {
        return !Mage::app()->isSingleStoreMode();
    }

    /**
     * Retrieve allowed for edit websites
     *
     * @return array
     */
    public function getWebsites()
    {
        if (!is_null($this->_websites)) {
            return $this->_websites;
        }

        $this->_websites = array(
            0   => array(
                'name'      => Mage::helper('catalog')->__('All Websites'),
                'currency'  => Mage::app()->getBaseCurrencyCode()
            )
        );

        if (!$this->isScopeGlobal() && $this->getProduct()->getStoreId()) {
            /* @var $website Mage_Core_Model_Website */
            $website = Mage::app()->getStore($this->getProduct()->getStoreId())->getWebsite();

            $this->_websites[$website->getId()] = array(
                'name'      => $website->getName(),
                'currency'  => $website->getBaseCurrencyCode()
            );
        } else if (!$this->isScopeGlobal()) {
            $websites           = Mage::app()->getWebsites(false);
            $productWebsiteIds  = $this->getProduct()->getWebsiteIds();
            foreach ($websites as $website) {
                /* @var $website Mage_Core_Model_Website */
                if (!is_null($productWebsiteIds) && !in_array($website->getId(), $productWebsiteIds)) {
                    continue;
                }
                $this->_websites[$website->getId()] = array(
                    'name'      => $website->getName(),
                    'currency'  => $website->getBaseCurrencyCode()
                );
            }
        }

        return $this->_websites;
    }

    /**
     * Retrieve default value for website
     *
     * @return int
     */
    public function getDefaultWebsite()
    {
        if ($this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite()) {
            return Mage::app()->getStore($this->getProduct()->getStoreId())->getWebsiteId();
        }
        return 0;
    }

    /**
     * Prepare global layout
     * Add "Add tier" button to layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Customer
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('catalog')->__('Add Tier'),
                'onclick'   => 'return customerPriceControl.addItem()',
                'class'     => 'add'
            ));
        $button->setName('add_customer_price_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Add Customer Price Item button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Returns customized price column header
     * that was seted through set...
     *
     * @param string $default
     * @return string
     */
    public function getPriceColumnHeader($default)
    {
        if ($this->hasData('price_column_header')) {
            return $this->getData('price_column_header');
        } else {
            return $default;
        }
    }

    /**
     * Returns customized price column header
     * that was seted through set...
     *
     * @param string $default
     * @return string
     */
    public function getPriceValidation($default)
    {
        if ($this->hasData('price_validation')) {
            return $this->getData('price_validation');
        } else {
            return $default;
        }
    }

    /**
     * Retrieve Customer Price entity attribute
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttribute()
    {
        return $this->getElement()->getEntityAttribute();
    }

    /**
     * Check customer price attribute scope is global
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getAttribute()->isScopeGlobal();
    }

    /**
     * Show customer prices grid website column
     *
     * @return bool
     */
    public function isShowWebsiteColumn()
    {
        if ($this->isScopeGlobal()) {
            return false;
        } else if (Mage::app()->isSingleStoreMode()) {
            return false;
        }
        return true;
    }

    /**
     * Check is allow change website value for combination
     *
     * @return bool
     */
    public function isAllowChangeWebsite()
    {
        if (!$this->isShowWebsiteColumn() || $this->getProduct()->getStoreId()) {
            return false;
        }
        return true;
    }
}
