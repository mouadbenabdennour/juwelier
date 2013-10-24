<?php

class MageModule_Customerprice_Block_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('customer_id');
        $this->setSaveParametersInSession(true);
    }

    public function setCollection($collection)
    {
        $collection->addAttributeToSelect('customer_id');
        return parent::setCollection($collection);
    }

    protected function _prepareColumns()
    {
        $ret = parent::_prepareColumns();
        // entity_id is right..
        $this->addColumn('entity_id', array(
           'header'    => $this->__('Customer Id'),
            'width'     => '50px',
            'index'     => 'customer_id',
        ));
        return $ret;
    }
}
