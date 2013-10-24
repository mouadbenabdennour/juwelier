<?php
class FME_Events_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$date = null;
		$dateParam = $this->getRequest()->getParam('date_event');
		if ($dateParam != null AND $dateParam != '')
		{
			$date = $dateParam;
		}
		
		Mage::register('eventDate',$date);
		$this->loadLayout();     
		$this->renderLayout();
    }
	
	public function viewAction()
	{
		$prefix = $this->getRequest()->getParam('pfx');
		$events = null;
  		if($prefix != null && $prefix != '')
		{
			$events = Mage::getModel('events/events')->loadByPrefix($prefix)->getData();
		}
		Mage::register('events', $events);
		
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function calendarAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}