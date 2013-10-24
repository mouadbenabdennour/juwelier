<?php

class FME_Events_Model_Mysql4_Events_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('events/events');
    }
    
    public function addStatusFilter($enabled = true)
	{
        $this->getSelect()->where('event_status = ?', $enabled ? 1 : 2);
        return $this;
    }
    
    public function addEventsFilter($events)
    {
        if (is_array($events))
        {
            $condition = $this->getConnection()->quoteInto('main_table.event_id IN(?)', $events);
        }
        else
        {
            $condition = $this->getConnection()->quoteInto('main_table.event_id= (?)', $events);
        }
        
        return $this->addFilter('event_id', $condition, 'string');
    }

    public function addStoreFilter($store)
    {
		if (!Mage::app()->isSingleStoreMode())
        {
            if ($store instanceof Mage_Core_Model_Store)
            {
                $store = array($store->getId());
            }

            $this->getSelect()->join(
                    array('store_table' => $this->getTable('events/events_store')),
                    'main_table.event_id = store_table.event_id',
                    array()
                    )
                    ->where('store_table.store_id in (?)', array(0, $store)); 
                   // $this->getSelect()->group();
            return $this;
        }
        return $this;
    }
}
