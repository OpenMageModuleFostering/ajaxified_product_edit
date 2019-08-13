<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Model_Resource_GridView extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('alekseon_ajaxifiedProductEdit/gridview', 'id');
    }
    
    public function saveAttributeColumns($gridView, $attributesData)
    {
        if (!is_array($attributesData)) {
            $attributesData = array();
        }
        
        $adapter = $this->_getWriteAdapter();
        
        $bind   = array(
            ':grid_view_id' => (int)$gridView->getId(),
        );
        
        $attributeColumsTable = $this->getTable('alekseon_ajaxifiedProductEdit/gridview_attributeColumns');
        
        $select = $adapter->select()
            ->from($attributeColumsTable, array('attribute_id', 'id'))
            ->where('grid_view_id = :grid_view_id');

        $currentAttributesColumns = $adapter->fetchPairs($select, $bind);

        $deleteIds = array();
        foreach($currentAttributesColumns as $attributeId => $id) {
            if (!isset($attributesData[$attributeId])) {
                $deleteIds[] = (int)$id;
            }
        }
        if (!empty($deleteIds)) {
            $adapter->delete($attributeColumsTable, array(
                'id IN (?)' => $deleteIds,
            ));
        }
        
        foreach ($attributesData as $attributeId => $attributeInfo) {
            if (isset($currentAttributesColumns[$attributeId])) {     
                $bind = array(
                    'editable'     => (bool)$attributeInfo['gridview_selectedattributes_editable'],
                    'position'     => (int)$attributeInfo['gridview_selectedattributes_position'],
                );
                $dataWhere  = array('id = ?' => $currentAttributesColumns[$attributeId]);
                $adapter->update($attributeColumsTable, $bind, $dataWhere);                
            } else {
                $bind = array(
                    'grid_view_id' => (int)$gridView->getId(),
                    'attribute_id' => (int)$attributeId,
                    'editable'     => (bool)$attributeInfo['gridview_selectedattributes_editable'],
                    'position'     => (int)$attributeInfo['gridview_selectedattributes_position'],
                );
                $adapter->insert($attributeColumsTable, $bind);
            }
        }
        
        return $this;
    }
}