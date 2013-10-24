<?php
class FME_Events_Block_Pager extends Mage_Page_Block_Html_Pager
{
    public function _construct()
    {
        parent::_construct();
    }

    public function getPagerUrl($params=array())
    {
        parent::getPagerUrl($params);
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        
        return $this->getUrl( Mage::helper('events')->extIdentifier(), $urlParams);
    }
    
    
}