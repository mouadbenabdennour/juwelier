<?php
class FME_Events_Block_Adminhtml_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$src = Mage::getBaseUrl('media'). DS .$row['event_thumb_image'];
		$a = "<img src ='".$src."' height ='48px'/>";
		
		return $a;
	}
}