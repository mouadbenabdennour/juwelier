<?php

class FME_Events_Block_Adminhtml_Events_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('events_form', array('legend'=>Mage::helper('events')->__('Event information')));
     
      $fieldset->addField('event_title', 'text', array(
          'label'     => Mage::helper('events')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'event_title',
      ));
	
	  $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

	  $fieldset->addField('event_start_date', 'date', array(
	   'name'   => 'event_start_date',
	   'label'  => $this->__('Event Start Date'),
	   'title'  => $this->__('Event Start Date'),
	   'image'  => $this->getSkinUrl('images/grid-cal.gif'),
	   'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
	   'format'       => $dateFormatIso,
	   'time' => true,
	   'style' => 'width:250px'
	  ));

	  $fieldset->addField('event_end_date', 'date', array(
          'name'   => 'event_end_date',
		  'label'  => $this->__('Event End Date'),
		  'title'  => $this->__('Event End Date'),
		  'image'  => $this->getSkinUrl('images/grid-cal.gif'),
		  'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
		  'format'       => $dateFormatIso,
		  'time' => true,
		  'style' => 'width:250px',
		  'required' => true
      ));
	  $fieldset->addField('kategory', 'Select', array(
			'label'     => Mage::helper('events')->__('Kategory'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'kategory',
			'onclick' => "",
			'onchange' => "",
			'value'  => '1',
			'values' => array(
							'-1'					=> 'Bitte auswählen',
							'Perfect Meat Academy' 	=> 'Perfect Meat Academy',
							'Weber Grillakademie' 	=> 'Weber Grillakademie', 
							'Otto Gourmet ON Tour' 	=> 'Otto Gourmet ON Tour', 
							'Kochschulen' 			=> 'Kochschulen', 
							'Special Editions' 		=> 'Special Editions'),
          'disabled' => false,
          'readonly' => false,
          //'after_element_html' => '',
          'tabindex' => 1
        ));

	  $fieldset->addField('event_venu', 'text', array(
          'label'     => Mage::helper('events')->__('Venue'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'event_venu',
      ));
	  
	  $fieldset->addField('event_url_prefix', 'text', array(
          'label'     => Mage::helper('events')->__('Url Prefix'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'event_url_prefix',
      ));
	  
      $fieldset->addField('event_image', 'image', array(
          'label'     => Mage::helper('events')->__('Image'),
          'required'  => false,
          'name'      => 'event_image',
		  'note'	  => Mage::helper('events')->__('<p>(upload image files only)</p>')	
	  ));
	  
	  $fieldset->addField('event_video', 'text', array(
          'label'     => Mage::helper('events')->__('Youtube Video Url'),
          'required'  => false,
          'name'      => 'event_video',
		  //'class'     => 'validate-url',
		  'after_element_html' => "<p><small>".Mage::helper('events')->__('Paste in video url from Youtube.')."</small></p>"
	  ));
	  
	  $fieldset->addField('stores','multiselect',array(
			'name'      => 'stores[]',
            'label'     => Mage::helper('events')->__('Store View'),
            'title'     => Mage::helper('events')->__('Store View'),
            'required'  => true,
			'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
	  ));
	  
      $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('tab_id' => 'form_section'));
      $wysiwygConfig["files_browser_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index');
      $wysiwygConfig["directives_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
      $wysiwygConfig["directives_url_quoted"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
      $wysiwygConfig["widget_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index');
	  $wysiwygConfig["files_browser_window_width"] = (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width');
	  $wysiwygConfig["files_browser_window_height"] = (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height');
      $plugins = $wysiwygConfig->getData("plugins");
      $plugins[0]["options"]["url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin');
      $plugins[0]["options"]["onclick"]["subject"] = "MagentovariablePlugin.loadChooser('".Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin')."', '{{html_id}}');";
      $plugins = $wysiwygConfig->setData("plugins",$plugins);
	  
      $fieldset->addField('event_content', 'editor', array(
          'name'      => 'event_content',
          'label'     => Mage::helper('events')->__('Content'),
          'title'     => Mage::helper('events')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'config'    => $wysiwygConfig,
          'required'  => true,
      ));
     
	  $fieldset->addField('event_status', 'select', array(
          'label'     => Mage::helper('events')->__('Status'),
          'name'      => 'event_status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('events')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('events')->__('Disabled'),
              ),
          ),
      ));
	  
      if ( Mage::getSingleton('adminhtml/session')->getEventsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getEventsData());
          Mage::getSingleton('adminhtml/session')->setEventsData(null);
      }
	  elseif ( Mage::registry('events_data') )
	  {//
          $form->setValues(Mage::registry('events_data')->getData());
      }
      return parent::_prepareForm();
  }
}
