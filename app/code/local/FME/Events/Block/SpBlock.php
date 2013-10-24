<?php
class FME_Events_Block_SpBlock extends Mage_Core_Block_Template
{
    public function eventsIn()
    {
        $events = Mage::helper('events')->eventsIn();
        
        if (!$events OR $events == '')
        {
            $events = null;
        }
        
        return $events;
    }
    
    public function spBlockTitle()
    {
        $title = "Events";
        
        if (Mage::getStoreConfig('events_options/basic_configs/sp_block_title') != '')
        {
            $title = Mage::getStoreConfig('events_options/basic_configs/sp_block_title');
        }
        
        return $title;
    }
}