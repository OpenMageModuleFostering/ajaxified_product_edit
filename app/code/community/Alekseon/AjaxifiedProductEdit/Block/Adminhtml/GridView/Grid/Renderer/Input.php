<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_GridView_Grid_Renderer_Input extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_attributeSets = array();
    protected $_inputBlock;
    protected $_useDefaultBlock;

    public function getInputBlock()
    {
        if (is_null($this->_inputBlock)) {
            $inputType = $this->getColumn()->getInputType();  
            $blockName = strtolower($inputType) . '_input';
            $this->_inputBlock = $this->getLayout()->getBlock($blockName);
        }
        return $this->_inputBlock;
    }
    
    public function getUseDefaultBlock()
    {
        if (is_null($this->_useDefaultBlock)) {
            $inputType = $this->getColumn()->getInputType();  
            $blockName = 'use_default';
            $this->_useDefaultBlock = $this->getLayout()->getBlock($blockName);
        }
        return $this->_useDefaultBlock;
    }

    public function render(Varien_Object $row)
    {
        $id = 'field[' . $row->getData('entity_id') . '][' . $this->getColumn()->getIndex() . ']';
        return '<span id="' . $id . '">' . $this->_renderField($row) . '</span>';
    }
    
    protected function _renderField(Varien_Object $row)
    {
        $column = $this->getColumn();
        $index = $column->getIndex();
        $attribute = $column->getAttribute();
 
        if ($attribute && $attribute->getApplyTo() && !in_array($row->getData('type_id'), $attribute->getApplyTo())) {
            return '<i style="color:grey">' . Mage::helper('alekseon_ajaxifiedProductEdit')->__('Not available for %s product.', $row->getData('type_id')) . '</i>';
        } elseif (!$this->_isAttributeInAttributeSet($index, $row->getData('attribute_set_id'))) {
            return '<i style="color:grey">' . Mage::helper('alekseon_ajaxifiedProductEdit')->__('Not available for this attribute set.') . '</i>';
        } elseif ($inputBlock = $this->getInputBlock()) {
            
            $attributeDefaultValue = null;
            if ($attribute && $attribute->getDefaultValue()) {       
                $attributeDefaultValue = $attribute->getDefaultValue();
            }

            $scope = $column->getScope();
            $value = is_null($row->getData($index)) ? $attributeDefaultValue : $row->getData($index);
            $defaultValue = is_null($row->getData($index . '_defaultValue')) ? $attributeDefaultValue : $row->getData($index . '_defaultValue');
            $rowId = $row->getData('entity_id');
            $name = 'product[' . $rowId . '][' . $index . ']';            

            $canUseDefault = false;
            $useDefault = false;

            if ($this->_getStoreId() && !is_null($scope) && $scope != Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL) {
                $canUseDefault = true;
                if ($value == $defaultValue) {
                    $useDefault = true;
                }
            }
            
            $inputBlock->setValue($value);
            $inputBlock->setName($name);
            $inputBlock->setRowId($rowId);
            $inputBlock->setRow($row->getData());
            $inputBlock->setColumn($column);
            $inputBlock->setDisabled(false);
            $inputBlock->setStore(Mage::app()->getStore($this->_getStoreId()));

            if ($useDefault) {
                $inputBlock->setHidden(true);
            } else {
                $inputBlock->setHidden(false);
            }

            $html = $inputBlock->_toHtml(); 
            
            if ($canUseDefault) {
                /* default input */
                $inputBlock->setValue($defaultValue);
                $inputBlock->setDisabled(true);
                $inputBlock->setName($name . '[default_value]');
                
                if ($useDefault) {
                    $inputBlock->setHidden(false);
                } else {
                    $inputBlock->setHidden(true);
                }

                $html .= $inputBlock->_toHtml();
                
                $useDefaultBlock = $this->getUseDefaultBlock();
                $useDefaultBlock->setIsChecked($useDefault);
                $useDefaultBlock->setRowId($rowId);
                $useDefaultBlock->setName($name);
                $html .= $useDefaultBlock->_toHtml();
            }

            return $html;
        } else {
            return '<i style="color:grey">' . Mage::helper('alekseon_ajaxifiedProductEdit')->__('Attribute type %s is not supported', '<b>' . $this->getColumn()->getInputType() . '</b>') . '</i>';
        }
    }

    protected function _getStoreId()
    {
        $storeId = Mage::registry('current_storeId', 0);
        return $storeId;
    }
    
    public function renderHeader()
    {
        if ($block = $this->getInputBlock()) {
            return parent::renderHeader();
        } else {        
            return $this->getColumn()->getHeader();
        }
    }
    
    protected function _isAttributeInAttributeSet($attributeCode, $attributeSetId)
    {
        if (!isset($this->_attributeSets[$attributeSetId])) {
            $attributesInSet = Mage::getResourceModel('catalog/product_attribute_collection')
                    ->setEntityTypeFilter('catalog_product')
                    ->setAttributeSetFilter($attributeSetId)
                    ->addStoreLabel($this->_getStoreId());
            
            $this->_attributeSets[$attributeSetId] = array('entity_id');
            
            foreach($attributesInSet as $attribute) {
                $this->_attributeSets[$attributeSetId][] = $attribute->getAttributeCode();
            }
        }
        
        return in_array($attributeCode, $this->_attributeSets[$attributeSetId]);
    }
}