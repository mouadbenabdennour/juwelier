<style rel="stylesheet" type="text/css">
@import "<?php echo Mage::getBaseUrl('skin').'frontend'. DS .'default'. DS .'default'. DS . 'css'. DS .'events'. DS; ?>calendar.css";
</style>
<?php
class FME_Events_Block_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
	public function _construct()
	{
		parent::_construct();
		$this->_availableMode = array('list' => $this->__('List'), 'grid' => $this->__('Grid'), 'calendar' => $this->__('Calendar'));
	}
	/**
     * Retrieve Pager URL
     *
     * @param string $order
     * @param string $direction
     * @return string
     */
    public function getOrderUrl($order, $direction)
    {
		parent::getOrderUrl($order,$direction);
        if (is_null($order)) {
            $order = $this->getCurrentOrder() ? $this->getCurrentOrder() : $this->_availableOrder[0];
        }
        return $this->getPagerUrl(array(
            $this->getOrderVarName()=>$order,
            $this->getDirectionVarName()=>$direction,
            $this->getPageVarName() => null
        ));
    }
	/**
     * Return current URL with rewrites and additional parameters
     * overriding parent action for custom work
     * @param array $params Query parameters
     * @return string
     **/
    public function getPagerUrl($params=array())
    {
		$mode = $this->_genMode($params);
		parent::getPagerUrl($params);
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
		$url = $this->getUrl(Mage::helper('events')->extIdentifier());
		$url = $url.'?'.$mode;
        return rtrim($url,'/');
    }
	/**
     * Retrive URL for view mode
     * overriding parent action for custom work
     * @param string $mode
     * @return string
     **/
    public function getModeUrl($mode)
    {
		if ($mode == 'calendar')
		{
			return $this->getUrl(trim(Mage::helper('events')->extIdentifier().'/calendar'));
		}
		
        return $this->getPagerUrl( array($this->getModeVarName()=>$mode, $this->getPageVarName() => null) );
    }

    public function getPagerHtml()
    {
        $pagerBlock = $this->getLayout()->createBlock('events/pager');
		
        if ($pagerBlock instanceof Varien_Object)
		{
            /* @var $pagerBlock Mage_Page_Block_Html_Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());
 
            $pagerBlock->setUseContainer(false)
            ->setShowPerPage(false)
            ->setShowAmounts(false)
            ->setLimitVarName($this->getLimitVarName())
            ->setPageVarName($this->getPageVarName())
            ->setLimit($this->getLimit())
            ->setCollection($this->getCollection()->distinct('event_id'));
			
            return $pagerBlock->toHtml();
        }
        return '';
    }
	/**
	 * making a custom mode for compatiblity with
	 * router performing concatination for variables
	 * @param array $params
	 * @return string $mode
	 **/
	protected function _genMode($params)
	{
		$mode = '';
		if (count($params > 1))
		{
			$i = 0;
			foreach ($params as $key => $val)
			{
				if ($i == 0)
				{
					$mode = trim($key.'='.$val);
				}
				elseif (isset($key) AND isset($val))
				{
					$mode .= '&'.trim($key.'='.$val);
				}
				
				$i++;
			}
		}
		
		return $mode;
	}
}

?>