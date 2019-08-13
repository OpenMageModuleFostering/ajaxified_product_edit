<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_GridView_Grid_Renderer_SaveButton extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {        
        $rowId = $row->getData('entity_id');

        $block = $this->getLayout()->getBlock('save_button');
        $block->setRowId($rowId);
        return $block->_toHtml();
    }
}