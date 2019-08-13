<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Model_System_Config_Source_UserRole
{
    protected $_options;
    
    public function toOptionArray()
    {
        if (is_null($this->_options)) {
            $collection =  Mage::getModel("admin/roles")->getCollection();                       
    
            $this->_options = array();
            
            foreach($collection as $role) {
                $this->_options[] = array(
                    'value' => $role->getRoleId(),
                    'label' => $role->getRoleName(),
                );
            }
        }

        return $this->_options;
    }
}
