<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Model_Resource_GridView_Attribute_Collection extends Mage_Catalog_Model_Resource_Product_Attribute_Collection
{
    protected $_gridViewId;

    public function setGridViewId($gridViewId)
    {
        $this->_gridViewId = $gridViewId;
        return $this;
    }
    
    protected function _beforeLoad()
    {
        $this->addVisibleFilter();
        if ($this->_gridViewId) {
            $this->_joinGridViewAttributeColumns();            
        }
        return parent::_beforeLoad();
    }    
    
    protected function _joinGridViewAttributeColumns()
    {
        $this->getSelect()->joinLeft(
            array('selected_attributes' => $this->getTable('alekseon_ajaxifiedProductEdit/gridview_attributeColumns')),
                'main_table.attribute_id = selected_attributes.attribute_id AND selected_attributes.grid_view_id = ' . $this->_gridViewId,
            array(
                'gridview_selectedattributes_editable'  => 'selected_attributes.editable',
                'gridview_selectedattributes_position' => 'selected_attributes.position',
                'gridview_selectedattributes_gridviewid' => 'selected_attributes.grid_view_id',
                'gridview_selectedattributes_attributeid' => 'selected_attributes.attribute_id',
            )
        );
        return $this;
    }
}