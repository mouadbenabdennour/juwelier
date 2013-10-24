<?php

class MageModule_Customerprice_Model_Dataflow_Convert_Adapter_Product extends Mage_Catalog_Model_Convert_Adapter_Product
{
    public function load()
    {
        $attrFilterArray = array();
        $attrFilterArray ['name']           = 'like';
        $attrFilterArray ['sku']            = 'startsWith';
        $attrFilterArray ['type']           = 'eq';
        $attrFilterArray ['attribute_set']  = 'eq';
        $attrFilterArray ['visibility']     = 'eq';
        $attrFilterArray ['status']         = 'eq';
        $attrFilterArray ['price']          = 'fromTo';
        $attrFilterArray ['qty']            = 'fromTo';
        $attrFilterArray ['store_id']       = 'eq';

        $attrToDb = array(
            'type'          => 'type_id',
            'attribute_set' => 'attribute_set_id'
        );

        $filters = $this->_parseVars();

        if ($qty = $this->getFieldValue($filters, 'qty')) {
            $qtyFrom = isset($qty['from']) ? $qty['from'] : 0;
            $qtyTo   = isset($qty['to']) ? $qty['to'] : 0;

            $qtyAttr = array();
            $qtyAttr['alias']       = 'qty';
            $qtyAttr['attribute']   = 'cataloginventory/stock_item';
            $qtyAttr['field']       = 'qty';
            $qtyAttr['bind']        = 'product_id=entity_id';
            $qtyAttr['cond']        = "{{table}}.qty between '{$qtyFrom}' AND '{$qtyTo}'";
            $qtyAttr['joinType']    = 'inner';

            $this->setJoinField($qtyAttr);
        }

        parent::setFilter($attrFilterArray, $attrToDb);

        if ($price = $this->getFieldValue($filters, 'price')) {
            $this->_filter[] = array(
                'attribute' => 'price',
                'from'      => $price['from'],
                'to'        => $price['to']
            );
            $this->setJoinAttr(array(
                'alias'     => 'price',
                'attribute' => 'catalog_product/price',
                'bind'      => 'entity_id',
                'joinType'  => 'LEFT'
            ));
        }

        // normally it would call the parent but to avoid that so much files have to be copied i just put the modified parent code here
        /** START CHANGE*/
        $ret = $this->parent_load();
        return $ret;
        /** END CHANGE*/
    }

    protected function parent_load()
    {
        if (!($entityType = $this->getVar('entity_type'))
            || !(Mage::getResourceSingleton($entityType) instanceof Mage_Eav_Model_Entity_Interface)) {
            $this->addException(Mage::helper('eav')->__('Invalid entity specified'), Varien_Convert_Exception::FATAL);
        }
        try {
            $collection = $this->_getCollectionForLoad($entityType);

            if (isset($this->_joinAttr) && is_array($this->_joinAttr)) {
                foreach ($this->_joinAttr as $val) {
//                    print_r($val);
                    $collection->joinAttribute(
                        $val['alias'],
                        $val['attribute'],
                        $val['bind'],
                        null,
                        strtolower($val['joinType']),
                        $val['storeId']
                    );
                }
            }

            $filterQuery = $this->getFilter();
            if (is_array($filterQuery)) {
                foreach ($filterQuery as $val) {
                    $collection->addFieldToFilter(array($val));
                }
            }

            $joinFields = $this->_joinField;
            if (isset($joinFields) && is_array($joinFields)) {
                foreach ($joinFields as $field) {
//                  print_r($field);
                    $collection->joinField(
                        $field['alias'],
                        $field['attribute'],
                        $field['field'],
                        $field['bind'],
                        $field['cond'],
                        $field['joinType']);
               }
           }

            /** START CHANGE*/
            $valueTable2 = 'cPrice';
            $collection->getSelect()->joinInner(array($valueTable2 => 'catalog_product_entity_customer_price'),
                "`e`.`entity_id`=`{$valueTable2}`.`entity_id`"
                );
            /** END CHANGE*/

           /**
            * Load collection ids
            */
           $entityIds = $collection->getAllIds();

           $message = Mage::helper('eav')->__("Loaded %d records", count($entityIds));
           $this->addException($message);
        }
        catch (Varien_Convert_Exception $e) {
            throw $e;
        }
        catch (Exception $e) {
            $message = Mage::helper('eav')->__('Problem loading the collection, aborting. Error: %s', $e->getMessage());
            $this->addException($message, Varien_Convert_Exception::FATAL);
        }

        /**
         * Set collection ids
         */
        $this->setData($entityIds);
        return $this;
    }

    /**
     * Save product (import)
     *
     * @param array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData)
    {
        $product = $this->getProductModel()
            ->reset();

        if ($this->getBatchParams('insert_mode') == 'delete_all_first')
        {
            if (!isset($_SESSION['customerprice']))
                $_SESSION['customerprice'] = array();
            if (!isset($_SESSION['customerprice']['batch']))
                $_SESSION['customerprice']['batch'] = array();
            $batchId = Mage::app()->getRequest()->getParam('batch_id');
            if (!isset($_SESSION['customerprice']['batch'][$batchId]))
            {
                $_SESSION['customerprice']['batch'][$batchId] = 'delete_all_first';
                Mage::getSingleton('core/resource')->getConnection('write')->delete('catalog_product_entity_customer_price', '');
            }
        }

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'store');
                Mage::throwException($message);
            }
        }
        else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skipping import row, store "%s" field does not exist.', $importData['store']);
            Mage::throwException($message);
        }

        if (empty($importData['sku'])) {
            $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }
        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);

        if ($productId) {
            $product->load($productId);
        }
        else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets();

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $value = isset($importData['type']) ? $importData['type'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
                Mage::throwException($message);
            }
            $product->setTypeId($productTypes[strtolower($importData['type'])]);
            /**
             * Check product define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, the value "%s" is invalid for field "%s"', $value, 'attribute_set');
                Mage::throwException($message);
            }
            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

            foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                    $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" for new products is not defined.', $field);
                    Mage::throwException($message);
                }
            }
        }

        $this->setProductTypeInstance($product);

        if (isset($importData['category_ids'])) {
            $product->setCategoryIds($importData['category_ids']);
        }

        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field])) {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                }
                catch (Exception $e) {}
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }

        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }

            $product->setData($field, $setValue);
        }

        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }

        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array();
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                }
                else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }
        $product->setStockData($stockData);

        $mediaGalleryBackendModel = $this->getAttribute('media_gallery')->getBackend();

        $arrayToMassAdd = array();

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            if (isset($importData[$mediaAttributeCode])) {
                $file = $importData[$mediaAttributeCode];
                if (trim($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
                    $arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
                }
            }
        }

        $addedFilesCorrespondence =
            $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes($product, $arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import', false, false);

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
                }
            }
        }

        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);

        $isNew = (!$product->getId());
        $product->save();

        /* our import data will look like this:
            customer_price:customer
            customer_price:qty
            customer_price:price
            customer_price:website
            customer_price:from
            customer_price:to
        */
        $customerPrice = array();
        $customerPrice['entity_id'] = $product->getId();
        foreach ($importData as $key => $value)
        {
            if (strpos($key, 'customer_price:') === 0)
            {
                $tmp = explode(':', $key);
                $key = $tmp[1];
                switch ($key)
                {
                    case 'customer':
                        $customerPrice['customer_id'] = $value;
                        break;
                    case 'qty':
                        $customerPrice['qty'] = (double)$value;
                        break;
                    case 'price':
                        $customerPrice['value'] = (double)$value;
                        break;
                    case 'website':
                        $customerPrice['website_id'] = (int)$value;
                        break;
                    case 'from':
                    case 'to':
                        $customerPrice[$key] = Mage::helper('customerprice/productprice')->formatDate($value);
                        break;
                }
            }
        }
        /* start customerId change */
        if (isset($customerPrice['customer_id']))
        {
            // the person who imports will work with customerId, which is different from the internal id used everywhere else - so we first need
            // to retrieve the internal customerId before we can continue
            $customerPrice['customer_id'] =
                Mage::helper('customerprice/productprice')->getIdByCustomerId($customerPrice['customer_id'],
                    (isset($customerPrice['website_id']))?$customerPrice['website_id']:0);
        }
        else
            $customerPrice['customer_id'] = 0;

        if (!$customerPrice['customer_id'])
        {
            Mage::throwException(
                Mage::helper('catalog')->__('Could not find any customer with this customer id.')
            );
        }
        /* end customerId change */

        if ($this->getBatchParams('insert_mode') == 'delete_first')
        {
            if (!isset($_SESSION['customerprice']))
                $_SESSION['customerprice'] = array();
            if (!isset($_SESSION['customerprice']['batch']))
                $_SESSION['customerprice']['batch'] = array();
            $batchId = Mage::app()->getRequest()->getParam('batch_id');
            if (!$batchId)
                $batchId = Mage::getSingleton('dataflow/batch')->getId();
            if (!isset($_SESSION['customerprice']['batch'][$batchId]))
                $_SESSION['customerprice']['batch'][$batchId] = array();
            if (!in_array($product->getId(), $_SESSION['customerprice']['batch'][$batchId]))
            {
                $_SESSION['customerprice']['batch'][$batchId][] = $product->getId();
                Mage::getSingleton('core/resource')->getConnection('write')->delete('catalog_product_entity_customer_price', 'entity_id='.$product->getId());
            }
        }

        // currently this doesn't look if the unique key constraint fails -> if so it just won't get inserted
        if ($customerPrice['customer_id'])
        {
            $value_id = Mage::getSingleton('core/resource')->getConnection('read')->fetchAll(Mage::getSingleton('core/resource')->getConnection('read')->select()
                ->from('catalog_product_entity_customer_price', array('value_id'))
                ->where('website_id='.((int)$customerPrice['website_id']).' AND qty='.((double)$customerPrice['qty']).' AND 
                customer_id="'.addslashes($customerPrice['customer_id']).'" AND entity_id='.$product->getId().' AND `from`="'.$customerPrice['from'].'"
                AND `to`="'.$customerPrice['to'].'"'));
            $value_id = (isset($value_id[0]))?$value_id[0]['value_id']:null;

            // update when it was found
            if ($value_id)
            {
                if (in_array($this->getBatchParams('insert_mode'), array('delete_first', 'delete_all_first')))
                {
                    Mage::throwException(
                        Mage::helper('catalog')->__('Duplicate website, customer price customer, quantity, from and to.')
                    );
                }
                Mage::getSingleton('core/resource')->getConnection('write')->update(
                        'catalog_product_entity_customer_price',
                        $customerPrice,
                        Mage::getSingleton('core/resource')->getConnection()->quoteInto('value_id = ?', $value_id));
            }
            else
            {
                Mage::getSingleton('core/resource')->getConnection('write')->insert(
                    'catalog_product_entity_customer_price',
                    $customerPrice);
            }
        }
    }
}
