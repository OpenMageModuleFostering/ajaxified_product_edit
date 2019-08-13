<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_GridView_Manager extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'alekseon_ajaxifiedProductEdit';
        $this->_controller = 'adminhtml_gridView_manager';
        $this->_headerText = Mage::helper('alekseon_ajaxifiedProductEdit')->__('Manage Grid Views');
        $this->_addButtonLabel = Mage::helper('alekseon_ajaxifiedProductEdit')->__('Add New Grid View');
        parent::__construct();
    }

}
