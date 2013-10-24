<?php
/**
 * Manufacturers extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Manufacturers
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net>
 * @copyright  Copyright 2010 � free-magentoextensions.com All right reserved
 */
 
class FME_Events_Block_Adminhtml_Events_Edit_Tab_Contact extends Mage_Adminhtml_Block_Widget_Form
{
    
    protected function _prepareForm()
    {
        
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contact_fieldset', array('legend' => Mage::helper('events')->__('Contact Detail')));
        
		$fieldset->addField('contact_name', 'text', array(
		  'label'     => Mage::helper('events')->__('Contact Person'),
		  'required'  => false,
		  'name'      => 'contact_name',
		));
		
    	$fieldset->addField('contact_phone', 'text', array(
            'name'		=> 'contact_phone',
            'label'		=> Mage::helper('events')->__('Phone'),
            'title'		=> Mage::helper('events')->__('Phone'),
    		'required'	=> false
        ));

    	$fieldset->addField('contact_fax', 'text', array(
            'name'		=> 'contact_fax',
            'label'		=> Mage::helper('events')->__('Fax'),
            'title'		=> Mage::helper('events')->__('Fax'),
    		'required'	=> false
        ));

        $fieldset->addField('contact_email', 'text', array(
            'name'		=> 'contact_email',
            'label'		=> Mage::helper('events')->__('Email'),
            'title'		=> Mage::helper('events')->__('Email'),
    	    'required'	=> false
        ));

        $fieldset->addField('contact_address', 'editor', array(
            'name'		=> 'contact_address',
            'label'		=> Mage::helper('events')->__('Address'),
            'title'		=> Mage::helper('events')->__('Address'),
    	    'required'	=> false
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