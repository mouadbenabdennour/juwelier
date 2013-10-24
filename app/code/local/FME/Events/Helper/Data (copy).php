<?php

class FME_Events_Helper_Data extends Mage_Core_Helper_Abstract
{
	const EVENT_PAGE_TITLE 			= 'events_options/seo_info/page_title';
	const EVENT_META_DESCRIPTION 	= 'events_options/seo_info/meta_description';
	const EVENT_META_KEYWORDS 		= 'events_options/seo_info/meta_keywords';
	const EXT_IDENTIFIER			= 'events_options/seo_info/events_url_prefix';
	/* Meta Seo related configurations */
	const OUT_OF_STOCK				= 'events_options/event_status_notifications/out_of_stock';
	const EXPIRED_EVENT				= 'events_options/event_status_notifications/expired_event';
	/* event notifications messages */
	const HEADER_LINK_TITLE			= 'events_options/basic_configs/header_link';
	const BOTTOM_LINK_TITLE			= 'events_options/basic_configs/bottom_link';
	/* header footer access links */
	const CALENDAR_LAYOUT			= 'events_options/events_pages_layouts/events_calendar_layout';
	const LANDING_LAYOUT			= 'events_options/events_pages_layouts/landing_layout';
	const EVENT_VIEW_LAYOUT			= 'events_options/events_pages_layouts/events_view_layout';
	const GRID_COLUMNS				= 'events_options/events_pages_layouts/grid_columns';
	/* pages layouts */
	const EVENTS_OF_DATE 			= 'events_options/basic_configs/static_block_events';
	const SHOW_MAP					= 'events_options/basic_configs/show_map';
	const BREADCRUMBS_ENABLE 		= 'events_options/basic_configs/breadcrumb_enable';
	const ERR_EMPTY_COLLECTION 		= 'events_options/event_status_notifications/err_empty_collection';
	
	public function linkTitleHeader()
	{
		return Mage::getStoreConfig(self::HEADER_LINK_TITLE);
	}
	
	public function linkTitleBottom()
	{
		return Mage::getStoreConfig(self::BOTTOM_LINK_TITLE);
	}
	
	public function isEnableMapShow()
	{
		return Mage::getStoreConfig(self::SHOW_MAP);
	}
	
	public function isEnableBreadcrumbs()
	{
		return (int) Mage::getStoreConfig(self::BREADCRUMBS_ENABLE);
	}
	/**
	 * to produce a number for columns in landing layout grid
	 * follwoing the magento default grid
	 * @return int $n the number for columns
	 * */
	public function numOfGridColumns()
	{
		$layout = (string)Mage::getStoreConfig(self::LANDING_LAYOUT);
		$n = 0;
		
		switch($layout)
		{
			case 'empty':
				$n = 6;
				break;
			case 'one_column':
                $n = 5;
                break;
            case 'two_columns_left':
                $n = 4;
                break;
            case 'two_columns_right':
                $n = 4;
                break;
            case 'three_columns':
                $n = 3;
                break;
			default:
				$n = 3;
		}
		
		return $n;
	}
	
	public function errMsg()
    {
        $err = "No event(s) registered under this date!";
        if (Mage::getStoreconfig(self::ERR_EMPTY_COLLECTION) != '')
        {
            $err = Mage::getStoreconfig(self::ERR_EMPTY_COLLECTION);
        }
        
        return $err;
    }
	
	public function getSeoInfo($info)
	{
		$data = '';
		switch ($info)
		{
			case 'title':
				$data = Mage::getStoreConfig(self::EVENT_PAGE_TITLE);
				break;
			case 'description':
				$data = Mage::getStoreConfig(self::EVENT_META_DESCRIPTION);
				break;
			case 'kywords':
				$data = Mage::getStoreConfig(self::EVENT_META_KEYWORDS);
				break;
		}
		
		return $data;
	}
	/**
	 * fetch notifications from store configuration
	 * @param int $type 0:out_of_stock, 1:expired_event
	 * @return string $data either empty or with value
	**/
	public function getNotificationType($type)
	{
		$data = '';
		switch($type)
		{
			case 0:
				$data = Mage::getStoreConfig(self::OUT_OF_STOCK);
				break;
			case 1:
				$data = Mage::getStoreConfig(self::EXPIRED_EVENT);
				break;
		}
		
		return $data;
	}
	
	public function getEventsFromCal()
	{
		return Mage::getStoreConfig(self::EVENTS_OF_DATE);
	}
	
	public function checkDuplicate($column,$value,$table,$id)
	{
		$isDuplicate = false;
		$_event = Mage::getModel('events/'.$table)->getCollection();
		$_event->addFieldToFilter($column, $value);
		
		if ($id)
		{
			$_event->addFieldToFilter('event_id', array('neq' => $id));
		}
		
		if ($_event->getData())
		{
			$isDuplicate = true;
		}
		
		return $isDuplicate;
	}
	
	public function fetchGallery($prefix)
	{
		$model = Mage::getModel('events/events')->loadByPrefix($prefix);
		$id = $model->getEventId();
		$pick = Mage::getSingleton('core/resource');
		$read = $pick->getConnection('core_read');
		$table = $pick->getTableName('events/events_gallery');
		
		$select = $read->select()->from(array('eg' => $table))
								->where('eg.events_id = (?)', (int)$id)
								->where('eg.image_status != (?)', 1)
								->order('eg.image_order');
						//echo $select;exit;		
		return $read->fetchAll($select);						
		
	}
	/*
	 * check event validity
	 * @param int $int event id
	 * @return bool
	*/
	public function isExpiredEvent($id)
	{
		$model = Mage::getModel('events/events')->load($id);
		$eventEndDate = $model->getEventEndDate();
		$currentDate = now();
		$isExpired = false;
		if ($currentDate > $eventEndDate)
		{
			$isExpired = true;
		}
		
		return $isExpired;
	}
	
	public function clientUrl()
	{
		return Mage::getUrl($this->extIdentifier());
	}
	
	public function extIdentifier()
	{
		$identifier = (string)Mage::getStoreConfig(self::EXT_IDENTIFIER);
		if ($identifier == '')
		{
			$identifier = 'all-events';
		}
		
		return $identifier;
	}
	
	public function customUrl($input='')
	{	
		return Mage::getUrl($this->extIdentifier().'/'.$input);
	}
	
	public function calendarLayout()
	{	
		return $this->getMyLayout((string)Mage::getStoreConfig(self::CALENDAR_LAYOUT));
	}
	
	public function landingLayout()
	{
		return $this->getMyLayout((string)Mage::getStoreConfig(self::LANDING_LAYOUT));
	}
	
	public function eventViewLayout()
	{
		return $this->getMyLayout((string)Mage::getStoreConfig(self::EVENT_VIEW_LAYOUT));
	}
	
	public function getMyLayout($input)
	{
		$layout = 'page/1column.phtml';
		
		switch($input)
		{
			case 'empty':
				$layout = 'page/empty.phtml';
				break;
			case 'one_column':
                $layout = "page/1column.phtml";
                break;
            case 'two_columns_left':
                $layout = 'page/2columns-left.phtml';
                break;
            case 'two_columns_right':
                $layout = 'page/2columns-right.phtml';
                break;
            case 'three_columns':
                $layout = 'page/3columns.phtml';
                break;
			default:
				$layout = 'page/1column.phtml';
		}
		
		return $layout;
	}
	
	public function isValidDate($date)
	{//echo date('d.m.Y ',strtotime($date));exit;
		$isValid = false;
		if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", trim(date('Y-m-d',strtotime($date))), $matches))
		{
			if (checkdate($matches[2], $matches[3], $matches[1]))
			{
				$isValid = true;
			}
		}
	
		return $isValid;
	}
	
	public function eventsIn()
	{
		$conn = Mage::getSingleton('core/resource');
		$tbl = $conn->getTableName('events/events');
		$read = $conn->getConnection('core_read');
		$dateDiff = (string)$this->getEventsFromCal();
		
		$date = new Zend_Db_Expr('CURDATE()'); 
		$curDay = $read->select()->from(array('evt' => $tbl))
						->where('DATE(evt.event_start_date) = (?)', $date)
						->where('evt.event_status = (?)', 1);
		
		$curWeek = "SELECT * FROM ".$tbl." WHERE WEEK(event_start_date) = WEEK(CURRENT_DATE) AND event_status = 1";
		
		$curMonth = "SELECT * FROM ".$tbl." WHERE MONTH(event_start_date) = MONTH(CURRENT_DATE) AND event_status = 1";
		
		$select = '';
		
		switch ($dateDiff)
		{
			case 'curr_day':
				$select = $curDay;
				break;
			case 'curr_week':
				$select = $curWeek;
				break;
			case 'curr_month':
				$select = $curMonth;
				break;
		}
		$q = $read->query($select);
		
		return $q->fetchAll();
	}
	/**
	 * to fetch all ids in 0 and current store
	 * than same ids will be use to fetch collection henced avoiding the
	 * duplication.
	 * @param int $storeId current store id
	 * */
	public function getSameIdsFromStores($storeId)
	{
		$m = Mage::getModel('events/events')->getCollection();
		$m->addStoreFilter($storeId);
		$m->addFieldToFilter('main_table.event_status', 1);
		$m->getSelect()->distinct('main_table.event_id');  
		$ids = array();
		$_data = $m->getData();
		if (!empty($_data))
		{
			foreach ($_data as $i)
			{
				$ids[] = $i['event_id'];
			}
		}
		
		return $ids;
	}
	/**
	 * to remove gallery images for an event.
	 * @param int $id
	 * @return unknown
	 **/
	public function removeGalleryByEventId($id)
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$write = $resource->getConnection('core_write');
		$table = $resource->getTableName('events_gallery');
		$eventMediaPath = Mage::getBaseDir('media'). DS .'events';
		$condition = $write->quoteInto('events_id = ?',$id,'INTEGER');
		$select = $read->select()
		->from(array('e_gallery'=>$table))
		->where('e_gallery.events_id = ?',$id);
		$gallery = $read->fetchAll($select); // echo '<pre>';print_r($gallery);exit;
		foreach ($gallery as $g){
			
			unlink($eventMediaPath.$g['image_file']);
		}
		
		$write->delete($table,$condition);
	}
}
