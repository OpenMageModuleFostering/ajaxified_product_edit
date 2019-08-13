<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
 class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_GridViewSwitcher extends Mage_Core_Block_Template
 {
    protected $_gridViewCollection;
 
    public function getGridViews()
    {
        if (is_null($this->_gridViewCollection)) {
            $this->_gridViewCollection = Mage::helper('alekseon_ajaxifiedProductEdit')->getAvailableGridViews();
        }
        return $this->_gridViewCollection;
    }
    
    public function getCurrentGridView()
    {
        return Mage::helper('alekseon_ajaxifiedProductEdit')->getCurrentGridView();
    }
    
    public function getSwitchUrl()
    {
        return $this->getUrl('*/*/*', array('_current' => true, 'view' => null));    
    }
 }