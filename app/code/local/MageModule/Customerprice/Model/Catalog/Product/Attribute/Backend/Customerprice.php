<?php
class MageModule_Customerprice_Model_Catalog_Product_Attribute_Backend_Customerprice 
  extends Mage_Catalog_Model_Product_Attribute_Backend_Price
{
    /**
     * Website currency codes and rates
     *
     * @var array
     */
    protected $_rates;

    /**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Customerprice
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('customerprice/product_attribute_backend_customerprice');
    }

    /**
     * Retrieve websites rates and base currency codes
     *
     * @return array
     */
    public function _getWebsiteRates()
    {
        if (is_null($this->_rates)) {
            $this->_rates = array();
            $baseCurrency = Mage::app()->getBaseCurrencyCode();
            foreach (Mage::app()->getWebsites() as $website) {
                /* @var $website Mage_Core_Model_Website */
                if ($website->getBaseCurrencyCode() != $baseCurrency) {
                    $rate = Mage::getModel('directory/currency')
                        ->load($baseCurrency)
                        ->getRate($website->getBaseCurrencyCode());
                    if (!$rate) {
                        $rate = 1;
                    }
                    $this->_rates[$website->getId()] = array(
                        'code' => $website->getBaseCurrencyCode(),
                        'rate' => $rate
                    );
                } else {
                    $this->_rates[$website->getId()] = array(
                        'code' => $baseCurrency,
                        'rate' => 1
                    );
                }
            }
        }
        return $this->_rates;
    }

    /**
     * Validate customer price data
     *
     * @param Mage_Catalog_Model_Product $object
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function validate($object)
    {
        $attribute  = $this->getAttribute();
        $tiers      = $object->getData($attribute->getName());
        if (empty($tiers)) {
            return true;
        }

        // validate per website
        $duplicates = array();
        foreach ($tiers as $tier) {
            if (!empty($tier['delete'])) {
                continue;
            }
            //$tier['from'] = Mage::helper('customerprice/productprice')->formatDate($tier['from']);
            //$tier['to'] = Mage::helper('customerprice/productprice')->formatDate($tier['to']);
            $compare = join('-', array($tier['website_id'], $tier['customer'], $tier['price_qty'] * 1, $tier['from'], $tier['to']));
            if (isset($duplicates[$compare])) {
                Mage::throwException(
                    Mage::helper('catalog')->__('Duplicate website customer price customer, quantity, from and to.')
                );
            }
            $duplicates[$compare] = true;
        }

        // if attribute scope is website and edit in store view scope
        // add global customer prices for duplicates find
        if (!$attribute->isScopeGlobal() && $object->getStoreId()) {
            $origTierPrices = $object->getOrigData($attribute->getName());
            foreach ($origTierPrices as $tier) {
                if ($tier['website_id'] == 0) {
                    //$tier['from'] = Mage::helper('customerprice/productprice')->formatDate($tier['from']);
                    //$tier['to'] =   Mage::helper('customerprice/productprice')->formatDate($tier['to']);
                    $compare = join('-', array($tier['website_id'], $tier['customer'], $tier['price_qty'] * 1, $tier['from'], $tier['to']));
                    $duplicates[$compare] = true;
                }
            }
        }

        // validate currency
        $baseCurrency = Mage::app()->getBaseCurrencyCode();
        $rates = $this->_getWebsiteRates();
        foreach ($tiers as $tier) {
            if (!empty($tier['delete'])) {
                continue;
            }
            if ($tier['website_id'] == 0) {
                continue;
            }

            $globalCompare = join('-', array(0, $tier['customer'], $tier['price_qty'] * 1));
            $websiteCurrency = $rates[$tier['website_id']]['code'];

            if ($baseCurrency == $websiteCurrency && isset($duplicates[$globalCompare])) {
                Mage::throwException(
                    Mage::helper('catalog')->__('Duplicate website customer price customer and quantity.')
                );
            }
        }

        return true;
    }

    /**
     * Prepare customer prices data for website
     *
     * @param array $priceData
     * @param string $productTypeId
     * @param int $websiteId
     * @return array
     */
    public function preparePriceData(array $priceData, $productTypeId, $websiteId)
    {
        $rates  = $this->_getWebsiteRates();
        $data   = array();
        $price  = Mage::getSingleton('catalog/product_type')->priceFactory($productTypeId);
        foreach ($priceData as $v) {
            $key = join('-', array($v['customer'], $v['price_qty']));
            if ($v['website_id'] == $websiteId) {
                $data[$key] = $v;
                $data[$key]['website_price'] = $v['price'];
            } else if ($v['website_id'] == 0 && !isset($data[$key])) {
                $data[$key] = $v;
                $data[$key]['website_id'] = $websiteId;
                if ($price->isTierPriceFixed()) {
                    $data[$key]['price'] = $v['price'] * $rates[$websiteId]['rate'];
                    $data[$key]['website_price'] = $v['price'] * $rates[$websiteId]['rate'];
                }
            }
        }

        return $data;
    }

    /**
     * Assign customer prices to product data
     *
     * @param Mage_Catalog_Model_Product $object
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Customerprice
     */
    public function afterLoad($object)
    {
        $storeId   = $object->getStoreId();
        $websiteId = null;
        if ($this->getAttribute()->isScopeGlobal()) {
            $websiteId = 0;
        } else if ($storeId) {
            $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        }

        $data = $this->_getResource()->loadPriceData($object->getId(), $websiteId);
        foreach ($data as $k => $v) {
            $data[$k]['website_price'] = $v['price'];
            $data[$k]['from'] = Mage::helper('customerprice/productprice')->unFormatDate($v['from']);
            $data[$k]['to'] =   Mage::helper('customerprice/productprice')->unFormatDate($v['to']);
        }

        if (!$object->getData('_edit_mode') && $websiteId) {
            $data = $this->preparePriceData($data, $object->getTypeId(), $websiteId);
        }

        $object->setData($this->getAttribute()->getName(), $data);
        $object->setOrigData($this->getAttribute()->getName(), $data);

        $valueChangedKey = $this->getAttribute()->getName() . '_changed';
        $object->setOrigData($valueChangedKey, 0);
        $object->setData($valueChangedKey, 0);

        return $this;
    }

    /**
     * After Save Attribute manipulation
     *
     * @param Mage_Catalog_Model_Product $object
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Customerprice
     */
    public function afterSave($object)
    {
        $websiteId  = Mage::app()->getStore($object->getStoreId())->getWebsiteId();
        $isGlobal   = $this->getAttribute()->isScopeGlobal() || $websiteId == 0;

        $customerPrices = $object->getData($this->getAttribute()->getName());
        if (empty($customerPrices)) {
            if ($isGlobal) {
                $this->_getResource()->deletePriceData($object->getId());
            } else {
                $this->_getResource()->deletePriceData($object->getId(), $websiteId);
            }
            return $this;
        }

        $old = array();
        $new = array();

        // prepare original data for compare
        $origCustomerPrices = $object->getOrigData($this->getAttribute()->getName());
        if (!is_array($origCustomerPrices)) {
            $origCustomerPrices = array();
        }
        foreach ($origCustomerPrices as $data) {
            //$data['from'] = Mage::helper('customerprice/productprice')->formatDate($data['from']);
            //$data['to'] =   Mage::helper('customerprice/productprice')->formatDate($data['to']);
            if ($data['website_id'] > 0 || ($data['website_id'] == '0' && $isGlobal)) {
                $key = join('-', array($data['website_id'], $data['customer'], $data['price_qty'] * 1));
                $old[$key] = $data;
            }
        }

        // prepare data for save
        foreach ($customerPrices as $data) {
            if (empty($data['price_qty']) || !isset($data['customer']) || !empty($data['delete'])) {
                continue;
            }
            if ($this->getAttribute()->isScopeGlobal() && $data['website_id'] > 0) {
                continue;
            }
            if (!$isGlobal && (int)$data['website_id'] == 0) {
                continue;
            }

            $key = join('-', array($data['website_id'], $data['customer'], $data['price_qty'] * 1, $data['from'], $data['to']));

            $customerId = $data['customer'];

            $data['from'] = Mage::helper('customerprice/productprice')->formatDate($data['from']);
            $data['to'] =   Mage::helper('customerprice/productprice')->formatDate($data['to']);
            $new[$key] = array(
                'website_id'        => $data['website_id'],
                'customer_id'       => $customerId,
                'qty'               => $data['price_qty'],
                'value'             => $data['price'],
                'from'               => $data['from'],
                'to'               => $data['to'],
            );
        }

        $delete = array_diff_key($old, $new);
        $insert = array_diff_key($new, $old);
        $update = array_intersect_key($new, $old);

        $isChanged  = false;
        $productId  = $object->getId();

        if (!empty($delete)) {
            foreach ($delete as $data) {
                $this->_getResource()->deletePriceData($productId, null, $data['price_id']);
                $isChanged = true;
            }
        }

        if (!empty($insert)) {
            foreach ($insert as $data) {
                $price = new Varien_Object($data);
                $price->setEntityId($productId);
                $this->_getResource()->savePriceData($price);

                $isChanged = true;
            }
        }

        if (!empty($update)) {
            foreach ($update as $k => $v) {
                if ($old[$k]['price'] != $v['value']) {
                    $price = new Varien_Object(array(
                        'value_id'  => $old[$k]['price_id'],
                        'value'     => $v['value']
                    ));
                    $this->_getResource()->savePriceData($price);

                    $isChanged = true;
                }
            }
        }

        if ($isChanged) {
            $valueChangedKey = $this->getAttribute()->getName() . '_changed';
            $object->setData($valueChangedKey, 1);
        }

        return $this;
    }
}
