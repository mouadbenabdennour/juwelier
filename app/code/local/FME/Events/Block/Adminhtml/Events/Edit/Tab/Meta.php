<?php

class FME_Events_Block_Adminhtml_Events_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{
    
    protected function _prepareForm()
    {
        
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('meta_fieldset', array('legend' => Mage::helper('events')->__('Meta Data')));
        
		$fieldset->addField('event_page_title', 'text', array(
		  'label'     => Mage::helper('events')->__('Page Title'),
		  'required'  => false,
		  'name'      => 'event_page_title',
		  'style'	  => 'width:280px'	
		));
		
    	$fieldset->addField('event_meta_keywords', 'editor', array(
            'name'		=> 'event_meta_keywords',
            'label'		=> Mage::helper('events')->__('Keywords'),
            'title'		=> Mage::helper('events')->__('Meta Keywords'),
    		'required'	=> false,
			'style'	  => 'width:280px'
        ));

    	$fieldset->addField('event_meta_description', 'editor', array(
            'name'		=> 'event_meta_description',
            'label'		=> Mage::helper('events')->__('Description'),
            'title'		=> Mage::helper('events')->__('Meta Description'),
    		'required'	=> false,
			'style'	  => 'width:280px'
        ));
        
	if ( Mage::getSingleton('adminhtml/session')->getEventsData() )
    {
        $form->setValues(Mage::getSingleton('adminhtml/session')->getEventsData());
        Mage::getSingleton('adminhtml/session')->setEventsData(null);
    }
	elseif ( Mage::registry('events_data') )
	{
        $form->setValues(Mage::registry('events_data')->getData());
    }
    return parent::_prepareForm();
        
    }
    
  
}