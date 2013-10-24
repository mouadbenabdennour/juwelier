<?php

class Webkochshop_Kundenpreise_Model_Entity_Attribute_Source_Type extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	const TYPE_FIX = 'fix';
	const TYPE_PERCENTAGE ='percentage';

	public function getAllOptions()
	{
		return array(
			array(
				'value' => self::TYPE_FIX,
				'label' => Mage::helper('kundenpreise')->__('Fix'),
			),
			array(
				'value' => self::TYPE_PERCENTAGE,
				'label' => Mage::helper('kundenpreise')->__('Percent'),
			),
		);
	}
}