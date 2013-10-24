<?php

class FME_Events_Model_Events extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('events/events');
    }
    /* for admin purpose only
      @return unknown
    */
    public function getImageList()
    {
        if (!$this->hasData('images_all'))
        {
            $_object = $this->_getResource()->loadImage();
        }
        
        return $this->getData('images_all');
    }
    /**
     * Retrieve related products
     * @return array
     */
    public function getEventsRelatedProducts($eventsId)
    {
        $events_productTable = Mage::getSingleton('core/resource')->getTableName('events_product');
        $collection = Mage::getModel('events/events')->getCollection()
                  ->addEventsFilter($eventsId);
                  //echo '<pre>';print_r($collection);exit;
        $collection->getSelect()
        ->joinLeft(array('related' => $events_productTable),
                    'main_table.event_id = related.eventid'
        )
        ->order('main_table.event_id');
        
        return $collection->getData();
    }
    
    public function getEventProducts($eventid)
    {
        $resource = Mage::getSingleton('core/resource');
        $_read = $resource->getConnection('core_read');
        $select = $_read->select()
                        ->from(array('p' => $resource->getTableName('events_product')),'p.product_id')
                        ->where('p.eventid = (?)',$eventid); 
        //$query = "SELECT products_id FROM " . $resource->getTableName('events_products')." WHERE eventsid ={$eventid}";
        $productIds = $_read->fetchOne($select);
        
        return $productIds;
    }
    
    public function loadByPrefix($prefix)
    {
        $this->_getResource()->loadByPrefix($this,$prefix);
        
        return $this;
    }
}