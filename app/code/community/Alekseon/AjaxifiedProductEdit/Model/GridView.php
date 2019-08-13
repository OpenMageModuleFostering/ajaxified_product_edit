<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Model_GridView extends Mage_Core_Model_Abstract
{
    public function __construct($args = array())
    {
		$this->_init('alekseon_ajaxifiedProductEdit/gridView');
    }
    
    public function isAvailable()
    {
        if (!$this->getId()) {
            return false;
        }
        
        if (!$this->getEnable()) {
            return false;
        }
    
        $currentUserRole = Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleId();
        
        $userRoles = unserialize($this->getUserRoles());

        if (is_array($userRoles)) {
            foreach($userRoles as $role) {
                if ($currentUserRole == $role) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    protected function _afterSave()
    {
        if ($this->getAttributesData()) {
            $this->getResource()->saveAttributeColumns($this, $this->getAttributesData());
        }
        return parent::_afterSave();
    }
}