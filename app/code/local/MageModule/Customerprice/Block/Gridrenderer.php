<?php

class MageModule_Customerprice_Block_Gridrenderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());

        if ($value)
        {
            if (Mage::getStoreConfig('customer/customer_id/auto_increment'))
            {
                if (Mage::getStoreConfig('customer/customer_id/template_force_apply'))
                {
                    if (preg_match('/[1-9]+[0-9]*/', $value, $result))
                    {
                        $number = $result[0];
                        $template = Mage::getStoreConfig('customer/customer_id/id_template');
                        $p = sscanf($value, $template);
                        if (!$p[0])
                        {
                            $value = sprintf($template, $number);
                        }
                    }
                }
            }
        }
        return $value;
    }
}

