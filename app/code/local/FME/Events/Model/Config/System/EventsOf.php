<?php
class FME_Events_Model_Config_System_EventsOf
{
	public function toOptionArray()
    {
        return array(
			array(
				'label' => Mage::helper('events')->__('Current Day'),
				'value' => 'curr_day'
			 ),
			array(
				'label' => Mage::helper('events')->__('Curent Week'),
				'value' => 'curr_week'
			),
			array(
				'label' => Mage::helper('events')->__('Current Month'),
				'value' => 'curr_month'
			),
        );
    }
}