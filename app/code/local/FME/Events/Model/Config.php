<?php
class FME_Events_Model_Config extends Mage_Catalog_Model_Product_Media_Config
{
    public function getBaseMediaPath()
	{
        return Mage::getBaseDir('media') .DS. 'events';
    }

    public function getBaseMediaUrl()
	{
        return Mage::getBaseUrl('media') . 'events';
    }

    public function getBaseTmpMediaPath()
	{
        return Mage::getBaseDir('media') .DS. 'tmp' .DS. 'events';
    }

    public function getBaseTmpMediaUrl()
	{
        return Mage::getBaseUrl('media') . 'tmp/events';
    }

}