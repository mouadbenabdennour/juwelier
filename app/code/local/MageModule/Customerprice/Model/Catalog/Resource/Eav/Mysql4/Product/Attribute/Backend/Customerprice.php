<?php
class MageModule_Customerprice_Model_Catalog_Resource_Eav_Mysql4_Product_Attribute_Backend_Customerprice 
    extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_attribute_customer_price', 'value_id');
    }

    /**
     * Load Customer Prices for product
     *
     * @param int $productId
     * @param int $websiteId
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Customerprice
     */
    public function loadPriceData($productId, $websiteId = null)
    {
        $adapter = $this->_getReadAdapter();
        $columns = array(
            'price_id'      => $this->getIdFieldName(),
            'website_id'    => 'website_id',
            'customer'      => 'customer_id',
            'price_qty'     => 'qty',
            'price'         => 'value',
            'from'=>'from',
            'to'=>'to',
        );
        $select  = $adapter->select()
            ->from($this->getMainTable(), $columns)
            ->where('entity_id=?', $productId)
            ->order('qty');
        if (!is_null($websiteId)) {
            if ($websiteId == '0') {
                $select->where('website_id=?', $websiteId);
            } else {
                $select->where('website_id IN(?)', array('0', $websiteId));
            }
        }

        return $adapter->fetchAll($select);
    }

    /**
     * Delete Customer Prices for product
     *
     * @param int $productId
     * @param int $websiteId
     * @param int $priceId
     * @return int The number of affected rows
     */
    public function deletePriceData($productId, $websiteId = null, $priceId = null)
    {
        $adapter = $this->_getWriteAdapter();
        $conds   = array(
            $adapter->quoteInto('entity_id=?', $productId)
        );
        if (!is_null($websiteId)) {
            $conds[] = $adapter->quoteInto('website_id=?', $websiteId);
        }
        if (!is_null($priceId)) {
            $conds[] = $adapter->quoteInto($this->getIdFieldName() . '=?', $priceId);
        }
        $where = join(' AND ', $conds);

        return $adapter->delete($this->getMainTable(), $where);
    }

    /**
     * Save customer price object
     *
     * @param Varien_Object $priceObject
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Customerprice
     */
    public function savePriceData(Varien_Object $priceObject)
    {
        $adapter = $this->_getWriteAdapter();
        $data    = $this->_prepareDataForTable($priceObject, $this->getMainTable());
        if (!empty($data[$this->getIdFieldName()])) {
            $where = $adapter->quoteInto($this->getIdFieldName() . '=?', $data[$this->getIdFieldName()]);
            unset($data[$this->getIdFieldName()]);
            $adapter->update($this->getMainTable(), $data, $where);
        } else {
            $adapter->insert($this->getMainTable(), $data);
        }
        return $this;
    }

    /**
     * Load product customer prices
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @deprecated since 1.3.2.3
     * @return array
     */
    public function loadProductPrices($product, $attribute)
    {
        $websiteId = null;
        if ($attribute->isScopeGlobal()) {
            $websiteId = 0;
        } else if ($product->getStoreId()) {
            $websiteId = Mage::app()->getStore($product->getStoreId())->getWebsiteId();
        }

        return $this->loadPriceData($product->getId(), $websiteId);
    }

    /**
     * Delete product customer price data from storage
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @deprecated since 1.3.2.3
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Customerprice
     */
    public function deleteProductPrices($product, $attribute)
    {
        $websiteId = null;
        if (!$attribute->isScopeGlobal()) {
            $storeId = $product->getProductId();
            if ($storeId) {
                $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
            }
        }

        $this->deletePriceData($product->getId(), $websiteId);

        return $this;
    }

    /**
     * Insert product Customer Price to storage
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $data
     * @deprecated since 1.3.2.3
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Customerprice
     */
    public function insertProductPrice($product, $data)
    {
        $priceObject = new Varien_Object($data);
        $priceObject->setEntityId($product->getId());

        return $this->savePriceData($priceObject);
    }
}
