<?php

class FME_Events_Block_Adminhtml_Events_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('events_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('events')->__('Event Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('events')->__('Event Information'),
          'title'     => Mage::helper('events')->__('Event Information'),
          'content'   => $this->getLayout()->createBlock('events/adminhtml_events_edit_tab_form')->toHtml(),
      ));
     
      $this->addTab('contact_form_section', array(
          'label'     => Mage::helper('events')->__('Contact Information'),
          'title'     => Mage::helper('events')->__('Contact Information'),
          'content'   => $this->getLayout()->createBlock('events/adminhtml_events_edit_tab_contact')->toHtml(),
      ));
      
      $this->addTab('meta_form_section', array(
          'label'     => Mage::helper('events')->__('Meta Information'),
          'title'     => Mage::helper('events')->__('Meta Information'),
          'content'   => $this->getLayout()->createBlock('events/adminhtml_events_edit_tab_meta')->toHtml(),
      ));
      
      $this->addTab('gallery_form_section', array(
          'label'     => Mage::helper('events')->__('Event Gallery'),
          'title'     => Mage::helper('events')->__('Event Gallery'),
          'content'   => $this->getLayout()->createBlock('events/adminhtml_events_edit_tab_image')->toHtml(),
      ));
      
      $this->addTab('products_section', array(
			'label'     => Mage::helper('events')->__('Attach With Products'),
			'url'       => $this->getUrl('*/*/products', array('_current' => true)),
			'class'     => 'ajax',
	  ));
      
      return parent::_beforeToHtml();
  }
}