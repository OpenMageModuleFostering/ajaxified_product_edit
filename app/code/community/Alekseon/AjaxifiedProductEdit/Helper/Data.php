<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
 class Alekseon_AjaxifiedProductEdit_Helper_Data extends Mage_Core_Helper_Abstract
 {
    protected $_currentGridView;
    protected $_currentGridViewsCollection; 
    
    public function getAlekseonEmail()
    {
        return 'contact@alekseon.com';
    }
    
    public function getAlekseonUrl()
    {   
        return 'http://www.alekseon.com';
    }
 
    public function getAttributeOptions($attribute)
    {
        $options = false;
    
        if ($source = $attribute->getSourceModel()) {
            $sourceModel = Mage::getModel($source);
            
            if (method_exists($sourceModel, 'getOptionArray')) {
                $options = $sourceModel->getOptionArray();
            } elseif (method_exists($sourceModel, 'getAllOptions')) {
                $sourceModel->setAttribute($attribute);
                $allOptions = $sourceModel->getAllOptions();                
                foreach($allOptions as $option) {
                    if (is_array($option['value'])) {
                        foreach($option['value'] as $subOption) {
                            $options[$subOption['value']] = $subOption['label'];
                        }
                    } else {
                        $options[$option['value']] = $option['label'];
                    }
                }                                
            }
        }
        
        return $options;
    }

    public function getAvailableGridViews()
    {
        if (is_null($this->_currentGridViewsCollection)) {
            $this->_currentGridViewsCollection = Mage::getModel('alekseon_ajaxifiedProductEdit/gridView')->getCollection();
       
            foreach($this->_currentGridViewsCollection as $gridView) {
                if (!$gridView->isAvailable()) {
                    $this->_currentGridViewsCollection->removeItemByKey($gridView->getId());
                }
            }
        }
        
        return $this->_currentGridViewsCollection;            
    }
 
    public function getCurrentGridView()
    {
        if (is_null($this->_currentGridView)) {
            $this->_currentGridView = Mage::registry('current_gridView', false);
        }
        
        return $this->_currentGridView;
    }

    public function getStaticFields()
    {
        return array('type' => array(
               'label' => Mage::helper('catalog')->__('Type'),
            ));
    }
 }