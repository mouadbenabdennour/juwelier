<?php

class FME_Events_Block_Adminhtml_Events_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('eventsGrid');
      $this->setDefaultSort('event_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _getStore()
  {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
  }
  protected function _prepareCollection()
  {
      $collection = Mage::getModel('events/events')->getCollection();
	  $store = $this->_getStore();
	  if ($store->getId())
	  {
		  $collection->addStoreFilter($store);
	  }
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('event_id', array(
          'header'    => Mage::helper('events')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'event_id',
      ));
	  
      $this->addColumn('event_title', array(
          'header'    => Mage::helper('events')->__('Title'),
          'align'     =>'left',
          'index'     => 'event_title',
      ));
	  
	  $this->addColumn('event_venu', array(
          'header'    => Mage::helper('events')->__('Venue'),
          'align'     =>'left',
          'index'     => 'event_venu',
      ));
	  
	  $this->addColumn('kategory', array(
          'header'    => Mage::helper('events')->__('Kategory'),
          'align'     =>'left',
          'index'     =>'kategory',
		  'type'      =>'options',
          'options'   => array(
                            -1 => 'Bitte auswählen',
							 1 => 'Perfect Meat Academy',
							 2 => 'Weber Grillakademie', 
							 3 => 'Otto Gourmet ON Tour', 
							 4 => 'Kochschulen', 
							 5 => 'Special Editions'),
      ));

	  
	  $this->addColumn('event_start_date', array(
          'header'    => Mage::helper('events')->__('Event Starting Date'),
          'align'     =>'left',
		  'type'	  => 'date',
		  'format'		=> Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
		  'width'	  => '200px',
          'index'     => 'event_start_date',
      ));
	  
	  $this->addColumn('event_end_date', array(
          'header'    => Mage::helper('events')->__('Event Ending Date'),
          'align'     =>'left',
		  'type'	  => 'date',
		  'format'		=> Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
		  'width'	  => '200px',
          'index'     => 'event_end_date',
      ));

	  $this->addColumn('event_url_prefix', array(
          'header'    => Mage::helper('events')->__('Event Url Prefix'),
          'align'     =>'left',
          'index'     => 'event_url_prefix',
      ));
	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('events')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('event_status', array(
          'header'    => Mage::helper('events')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'event_status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
	  $this->addColumn('event_image', array(
		  'header'=> Mage::helper('events')->__('Event-Image'),
		  'width' => '50px',
		  'index' => 'event_image',
		  'renderer' => 'FME_Events_Block_Adminhtml_Renderer_Image',
		  'filter' => false
	  ));
	  
      $this->addColumn('action', array(
		  'header'    =>  Mage::helper('events')->__('Action'),
		  'width'     => '100',
		  'type'      => 'action',
		  'getter'    => 'getId',
		  'actions'   => array(
			  array(
				  'caption'   => Mage::helper('events')->__('Edit'),
				  'url'       => array('base'=> '*/*/edit'),
				  'field'     => 'id'
			  )
		  ),
		  'filter'    => false,
		  'sortable'  => false,
		  'index'     => 'stores',
		  'is_system' => true,
      ));
		
	  $this->addExportType('*/*/exportCsv', Mage::helper('events')->__('CSV'));
	  $this->addExportType('*/*/exportXml', Mage::helper('events')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('event_id');
        $this->getMassactionBlock()->setFormFieldName('events');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('events')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('events')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('events/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('event_status', array(
             'label'=> Mage::helper('events')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'event_status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('events')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}
