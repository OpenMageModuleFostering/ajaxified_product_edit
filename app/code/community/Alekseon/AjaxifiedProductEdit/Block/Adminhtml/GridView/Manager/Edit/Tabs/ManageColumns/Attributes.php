<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_GridView_Manager_Edit_Tabs_ManageColumns_Attributes extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_selectedAttributesIds;

    public function __construct()
    {
        parent::__construct();
        $this->setId('alekseon_ajaxifiedProductEdit_gridView_columns_grid');
        
        if ($this->_getGridView()->getId()) {
            $this->setDefaultSort('gridview_selectedattributes_position');            
        } else {
            $this->setDefaultSort('attribute_code');            
        }
        $this->setDefaultDir('ASC');

        $this->setUseAjax(true);
        if ($this->_getGridView()->getId()) {
            $this->setDefaultFilter(array('is_selected' => 1));
        }
    }
    
    protected function _getGridView()
    {
        return Mage::registry('current_gridView');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('alekseon_ajaxifiedProductEdit/gridView_attribute_collection');
        $collection->setGridViewId($this->_getGridView()->getId());
		$this->setCollection($collection);  
  
        return parent::_prepareCollection();       
	}
    
	protected function _prepareColumns()
    {
        $this->addColumn('is_selected', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'name'              => 'is_selected',
            'values'            => $this->_getSelectedAttributesIds(),
            'align'             => 'center',
            'index'             => 'attribute_id'
        ));
    
        $this->addColumn('attribute_code', array(
            'header'   => Mage::helper('alekseon_ajaxifiedProductEdit')->__('Attribute Code'),
            'sortable' => true,
            'index'    => 'attribute_code'
        ));
    
        $this->addColumn('frontend_label', array(
            'header'   => Mage::helper('alekseon_ajaxifiedProductEdit')->__('Attribute Label'),
            'sortable' => true,
            'index'    => 'frontend_label'
        ));

        $optionsYesNo = array(
            0 => Mage::helper('alekseon_ajaxifiedProductEdit')->__('No'),
            1 => Mage::helper('alekseon_ajaxifiedProductEdit')->__('Yes'),
         );
        
        $this->addColumn('gridview_selectedattributes_editable', array(
            'header'   => Mage::helper('alekseon_ajaxifiedProductEdit')->__('Is Editable On Grid View'),
            'sortable' => true,
            'name'     => 'gridview_selectedattributes_editable',
            'options'  => $optionsYesNo,
            'type'     => 'options',
            'renderer' => 'adminhtml/widget_grid_column_renderer_select',
            'index'    => 'gridview_selectedattributes_editable',
            'filter_index'  => 'selected_attributes.editable',
            'editable'      => true,
        ));

        $this->addColumn('gridview_selectedattributes_position', array(
            'header'            => Mage::helper('alekseon_ajaxifiedProductEdit')->__('Position'),
            'name'              => 'gridview_selectedattributes_position',
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'gridview_selectedattributes_position',
            'width'             => 60,
            'filter_index'      => 'selected_attributes.position',
            'editable'          => true,
        ));

        return parent::_prepareColumns();
    }
    
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'is_selected') {
            $attributesIds = $this->_getSelectedAttributesIds();
            if (empty($attributesIds)) {
                $attributesIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.attribute_id', array('in' => $attributesIds));
            } else {
                if($attributesIds) {
                    $this->getCollection()->addFieldToFilter('main_table.attribute_id', array('nin' => $attributesIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    protected function _getSelectedAttributesIds()
    {
        $attributesIds = $this->getSelectedAttributesIds();

        if (!is_array($attributesIds)) {
            $attributesIds = array_keys($this->_getSelectedAttributes());
        }
        return $attributesIds;
    }
    
    protected function _getSelectedAttributes()
    {
        if (is_null($this->_selectedAttributesIds)) {
            $this->_selectedAttributesIds = array();
            if ($this->_getGridView()->getId()) {
                $collection = Mage::getResourceModel('alekseon_ajaxifiedProductEdit/gridView_attribute_collection');
                $collection->setGridViewId($this->_getGridView()->getId());
                foreach($collection as $attribute) {
                    if ($attribute->getGridviewSelectedattributesGridviewid() == $this->_getGridView()->getId()) {
                        $this->_selectedAttributesIds[$attribute->getAttributeId()] = $attribute;
                    }
                }
            }
        }

        return $this->_selectedAttributesIds;
    }
    
    public function getSelectedAttributes()
    {
        $attributes = array();
        foreach ($this->_getSelectedAttributes() as $attribute) {
            $attributeData = array(
                                'gridview_selectedattributes_position' => $attribute->getGridviewSelectedattributesPosition(),
                                'gridview_selectedattributes_editable' => $attribute->getGridviewSelectedattributesEditable(),
                             );
            $attributes[$attribute->getAttributeId()] = $attributeData;
        }
        return $attributes;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/manageColumnsGrid', array('_current' => true));
    }
    
    public function getRowUrl($row)
    {
        return false;
    }
}
