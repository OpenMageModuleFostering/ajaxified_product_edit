<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_GridView_Manager_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {            
        $this->_mode = 'edit';
        $this->_blockGroup = 'alekseon_ajaxifiedProductEdit';
        $this->_controller = 'adminhtml_gridView_manager';

        parent::__construct();
    }
    
    public function getHeaderText()
    {
        if ($this->_getCurrentGridView()->getId()) {
            return Mage::helper('alekseon_ajaxifiedProductEdit')->__("Edit Grid View '%s'", $this->escapeHtml($this->_getCurrentGridView()->getLabel()));
        } else {
            return Mage::helper('alekseon_ajaxifiedProductEdit')->__('New Grid View');
        }
    }
    
    protected function _getCurrentGridView()
    {
        return Mage::helper('alekseon_ajaxifiedProductEdit')->getCurrentGridView();
    }
}
