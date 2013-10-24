<?php

class FME_Events_Block_Adminhtml_Events_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'events';
        $this->_controller = 'adminhtml_events';
        
        $this->_updateButton('save', 'label', Mage::helper('events')->__('Save Event'));
        $this->_updateButton('delete', 'label', Mage::helper('events')->__('Delete Event'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('events_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'events_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'events_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
	
	protected function _prepareLayout()
    {
		parent::_prepareLayout();
		
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled())
		{
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		}
    }
	
    public function getHeaderText()
    {
        if( Mage::registry('events_data') && Mage::registry('events_data')->getId() ) {
            return Mage::helper('events')->__("Edit Event '%s'", $this->htmlEscape(Mage::registry('events_data')->getEventTitle()));
        } else {
            return Mage::helper('events')->__('Add Event');
        }
    }
}