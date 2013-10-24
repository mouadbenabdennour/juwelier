<?php
class FME_Events_Model_Config_System_Eventsview
{
	public function toOptionArray()
	{
		return array(
			array(
				'label' => Mage::helper('events')->__('List'),
				'value' => 'list'
			),
			array(
				'label' => Mage::helper('events')->__('Grid'),
				'value' => 'grid'
			),
			//array(
			//	'label' => Mage::helper('events')->__('Calender'),
			//	'value' => 'calendar'
			//)
		);
	}
}