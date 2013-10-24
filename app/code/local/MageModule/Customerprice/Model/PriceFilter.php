<?php

class MageModule_Customerprice_Model_PriceFilter extends Mage_Catalog_Model_Resource_Layer_Filter_Price
{
    protected $_fullCollection = null; // fullcollection is the layercollection without page-limitation
    // problem is, that the originalcollection has already pagesize limit on it but we need all
    protected function getFullCollection($filter)
    {
        if (!$this->_fullCollection)
        {
            $origCollection = $filter->getLayer()->getProductCollection();
            $collection = clone $origCollection;
            $collection->clear();
            $collection->setPageSize(9999);
            $this->_fullCollection = $collection;
        }
        return $this->_fullCollection;
    }

    public function getMaxPrice($filter)
    {
        $collection = $this->getFullCollection($filter);
        $max = 0;
        foreach ($collection->getItems() as $v)
        {
            $price = $v->getPrice();
            if ($price > $max)
                $max = $price;
        }
        return $max;
    }

    public function getCount($filter, $range)
    {
        $ret = array();

        $collection = $this->getFullCollection($filter);

        foreach ($collection->getItems() as $v)
        {
            $price = $v->getPrice();
            $i = ceil($price / $range);
            if (!isset($ret[$i]))
                $ret[$i] = 0;
            $ret[$i]++;
        }
        if (isset($ret[0]))
        {
            $ret[1] += $ret[0];
            unset($ret[0]);
        }
        ksort($ret);
        return $ret;
    }

    public function applyFilterToCollection($filter, $range, $index)
    {
        $collection = $this->getFullCollection($filter);

        $items = array();
        foreach($collection->getItems() as $k=>$v)
        {
            $price = $v->getPrice();
            if ($price < ($index-1)*$range || $price > $index*$range)
                continue;
            $items[$k] = $v;
        }

        $entities = array();
        foreach($items as $it)
            $entities[] = $it->getData('entity_id');

        $collection = $filter->getLayer()->getProductCollection();
        $collection->addPriceData($filter->getCustomerGroupId(), $filter->getWebsiteId());
        $collection->addAttributeToFilter('entity_id',array('in'=>$entities));
        return $this;
    }
}
