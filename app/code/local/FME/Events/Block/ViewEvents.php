<?php
class FME_Events_Block_ViewEvents extends Mage_Core_Block_Template
{
	public function _prepareLayout()
	{
		$prefix = $this->getRequest()->getParam('pfx');
		$dets = Mage::getModel('events/events')->loadByPrefix($prefix);
		/* loading info with custom method */
		if ($head = $this->getLayout()->getBlock('head'))
		{
		    if($dets->getEventTitle() != '')
			{
			    $head->setTitle($dets->getEventTitle());
		    } 
		    
		    if($dets->getEventMetaKeywords() != '')
			{
			    $head->setKeywords($dets->getEventMetaKeywords());
		    } 
		    
		    if($dets->getEventMetaDescription() != '')
			{
			    $head->setDescription($dets->getEventMetaDescription());
		    }
		}
		/* setting event particular seo info */
		if (Mage::helper('events')->isEnableBreadcrumbs())
		{
			$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
			$breadcrumbs->addCrumb('events', array(
				'label' => Mage::helper('cms')->__(Mage::helper('events')->linkTitleHeader()),
				'title' => Mage::helper('cms')->__(Mage::helper('events')->linkTitleHeader()),
				'link' => Mage::helper('events')->clientUrl()
			));
			$breadcrumbs->addCrumb('events_view', array(
				'label' => Mage::helper('cms')->__($dets->getEventTitle()),
				'title' => Mage::helper('cms')->__($dets->getEventTitle()),
				'link' => false
			));
		}
		
		return parent::_prepareLayout();
	}
	
	public function getEventProduct()
	{
		$prefix = $this->getRequest()->getParam('pfx');
        $events = Mage::getModel('events/events')->loadByPrefix($prefix);
		$eventId = (int)$events->getEventId();
        $product = $events->getEventProducts($eventId); 
		
        return $product;
	}
	
	public function getEvents()     
    { 
       $block = $this->getLayout()->createBlock('events/events', microtime());
		
       return $block->getEvents();
    }
	
	public function getEventGallery()
	{
		$prefix = $this->getRequest()->getParam('pfx');
		
		return Mage::helper('events')->fetchGallery($prefix);
	}
}