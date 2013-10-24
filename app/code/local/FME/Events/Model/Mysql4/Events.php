<?php

class FME_Events_Model_Mysql4_Events extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the event_id refers to the key field in your database table.
        $this->_init('events/events', 'event_id');
    }
    
    public function loadImage(Mage_Core_Model_Abstract $object)
    {
        return $this->__loadImage($object);
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!empty($object['images'])) 
        {
            $this->__saveEventImages($object);
        }
        
        $condition = $this->_getWriteAdapter()->quoteInto('event_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('events_store'), $condition);
		
		$conditionProduct = $this->_getWriteAdapter()->quoteInto('eventid = ?', $object->getId());
		$this->_getWriteAdapter()->delete($this->getTable('events_product'),$conditionProduct);
        foreach ((array)$object->getData('stores') as $store)
        {
            $storeArray = array();
            $storeArray['event_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('events_store'), $storeArray);
        }
        
        $links = $object['links']; // echo '<pre>'; print_r($links);exit;
        if (isset($links['related']))
        {
            $productIds = Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']); // echo '<pre>';print_r($productIds);exit;
            
            foreach ($productIds as $_p)
            {
                $objArr = array();
                $objArr['eventid'] = $object->getId();
                $objArr['product_id'] = $_p;
                $this->_getWriteAdapter()->insert($this->getTable('events_product'),$objArr);
            }
        }
        return parent::_afterSave($object);
    }
    
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassDelete())
        {
            $object = $this->__loadImage($object);
        }
        
        $products = $this->__listProducts($object);
        if ($products)
        {
            $object->setData('product_id', $products);
        }
        
        $selectStores = $this->_getReadAdapter()->select()
                                              ->from($this->getTable('events/events_store'))
                                              ->where('event_id = (?)', $object->getId());
                                        
        $storesData = $this->_getReadAdapter()->fetchAll($selectStores); // echo '<pre>'; print_r($storesData);exit; 
        if ($storesData)
        {
            $storeIds = array();
            foreach ($storesData as $_row)
            {
                $storeIds[] = $_row['store_id'];
            }
           
            $object->setData('stores', $storeIds);
        }
        
        return parent::_afterLoad($object);
    }
    
    private function __loadImage(Mage_Core_Model_Abstract $object)
    {
        $_q = $this->_getReadAdapter()->select()
            ->from($this->getTable('events/events_gallery'))
            ->where('events_id = (?)', $object->getId())
            ->order(array('image_order ASC','image_name'));
            
        $object->setData('images_all', $this->_getReadAdapter()->fetchAll($_q));
        
        return $object;
    }
    /* a pvt function all images to corresponding to current event id
      will store in separate table.
      @param Mage_Core_Model_Abstract $object
    */
    private function __saveEventImages(Mage_Core_Model_Abstract $object)
    {
        $_imgArr = array();
        $_imgArr = Zend_Json::decode($object->getData('images')); // echo '<pre>';print_r($_imgArr);exit;
        if (is_array($_imgArr) AND sizeof($_imgArr) > 0)
        {
            $_imgTable = $this->getTable('events/events_gallery');
            $_adapter = $this->_getWriteAdapter();
            $_adapter->beginTransaction();
            try
            {
                $_condition = $_adapter->quoteInto('events_id = (?)', $object->getId());
                $this->_getWriteAdapter()->delete($this->getTable('events/events_gallery'), $_condition);
                foreach ($_imgArr as $_i)
                {
                    if (isset($_i['removed']) && $_i['removed'] == '1')
                    {
                        $_adapter->delete($_imgTable,$_adapter->quoteInto('image_id = (?)', $_i['value_id']),'INTEGER');
                    }
                    else
                    {
                        $_data = array(
                                    'image_file' => $_i['file'],
                                    'image_name' => $_i['label'],
                                    'image_order' => $_i['position'],
                                    'image_status' => $_i['disabled'],
                                    'events_id' => $object->getId()
                                 );
                        $_adapter->insert($_imgTable, $_data);
                    }
                }
                $_adapter->commit();
            }
            catch(Exception $e)
            {
                $_adapter->rollBack();
                echo $e->getMessage();exit;
            }
        }
    }
    
    private function __listProducts(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
                                         ->from($this->getTable('events/events_product'))
                                         ->where('eventid = ?', $object->getId());
        $data = $this->_getReadAdapter()->fetchAll($select);
        if ($data)
        {
            $productsArr = array();
            foreach ($data as $_i)
            {
                $productsArr[] = $_i['product_id'];
            }
            
            return $productsArr;
        }
    }
    
    public function loadByPrefix(FME_Events_Model_Events $obj,$prefix)
    {
        $select = $this->_getReadAdapter()->select()
                        ->from($this->getMainTable())
                        ->where($this->getMainTable().'.event_url_prefix = (?)', $prefix);
        $id = $this->_getReadAdapter()->fetchOne($select);
        if ($id)
        {
            $this->load($obj,$id);
        }
        
        return $this;
    }
}
