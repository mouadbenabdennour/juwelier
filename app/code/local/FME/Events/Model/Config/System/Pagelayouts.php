<?php
class FME_Events_Model_Config_System_Pagelayouts
{
	public function toOptionArray()
    {
        return array(
			array(
				'label' => Mage::helper('events')->__('Empty'),
				'value' => 'empty'
			 ),
			array(
				'label' => Mage::helper('events')->__('1 column'),
				'value' => 'one_column'
			),
			array(
				'label' => Mage::helper('events')->__('2 columns with left bar'),
				'value' => 'two_columns_left'
			),
			array(
				'label' => Mage::helper('events')->__('2 column with right bar'),
				'value' => 'two_columns_right'
			),
			array(
				'label' => Mage::helper('events')->__('3 columns'),
				'value' => 'three_columns'
			)
        );
    }
}