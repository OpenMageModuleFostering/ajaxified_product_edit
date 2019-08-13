<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_GridView_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_attributesColumns;
    protected $_selectedAttributes;

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid_view_' . $this->_getCurrentGridView()->getId());
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareLayout()
    {    
        $this->setChild('attributes_selector',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Select attributes'),
                    'onclick'   => "$('attributes_selector').show(); return false;",
                    'class'   => 'task'
                ))
        );        
    
        $this->setChild('reset_filter_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Reset Filter'),
                    'onclick'   => 'if (alertIfDataHasChanged()) {' . $this->getJsObjectName().'.resetFilter(); } else { return false; }',
                ))
        );

        $this->setChild('search_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Search'),
                    'onclick'   => 'if (alertIfDataHasChanged()) {' . $this->getJsObjectName().'.doFilter(); } else { return false; }',
                    'class'   => 'task'
                ))
        );          

        return Mage_Adminhtml_Block_Widget::_prepareLayout();
    }

    public function getMainButtonsHtml()
    {
        $html = '';
        if($this->getFilterVisibility()) {
            $html.= $this->getResetFilterButtonHtml();
            $html.= $this->getSearchButtonHtml();
        }
        return $html;
    }    
    
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _getCurrentGridView()
    {
        return Mage::helper('alekseon_ajaxifiedProductEdit')->getCurrentGridView();
    }
    
    protected function _getSelectedAttributes()
    {
        $gridView = $this->_getCurrentGridView();
        if (is_null($this->_selectedAttributes)) {
            $this->_selectedAttributes = array();
            $collection = Mage::getResourceModel('alekseon_ajaxifiedProductEdit/gridView_attribute_collection');
            $collection->setGridViewId($gridView->getId());
            $collection->setOrder('gridview_selectedattributes_position', 'ASC');
            foreach($collection as $attribute) {
                if ($attribute->getGridviewSelectedattributesGridviewid() == $gridView->getId()) {
                    $this->_selectedAttributes[] = $attribute;
                }
            }
        }

        return $this->_selectedAttributes;
    }

    
    protected function _getAttributesColumns()
    {
        if (is_null($this->_attributesColumns)) {
            
            $this->_attributesColumns = array();
            foreach($this->_getSelectedAttributes() as $attribute) {
                if ($attribute->getId()) {  
                    $columnData = array(
                        'attribute' => $attribute,
                        'label'    => $attribute->getFrontendLabel(),
                        'scope'    => $attribute->getIsGlobal(),
                        'input'    => $attribute->getFrontendInput(),
                        'options'  => Mage::helper('alekseon_ajaxifiedProductEdit')->getAttributeOptions($attribute),                        
                        'editable' => false,
                        'renderer' => false,
                    );
                    
                    if ($attribute->getGridviewSelectedattributesEditable()) {
                       $columnData['renderer'] = 'alekseon_ajaxifiedProductEdit/adminhtml_gridView_grid_renderer_input';
                       $columnData['editable'] = true;
                    }                    
                
                    $this->_attributesColumns[$attribute->getAttributeCode()] = $columnData;                    
                }
            }
        }

        return $this->_attributesColumns;
    }
    
    protected function _prepareCollection($productIds = array())
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection();
        
        if (!empty($productIds)) {
            $collection->addFieldToFilter('entity_id', array('in' => $productIds));
        }

        if ($store->getId()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            foreach($this->_getAttributesColumns() as $attribute => $data) {
                if ($data['scope'] !== false && $data['scope'] != Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL) {
                    $collection->joinAttribute($attribute, 'catalog_product/' . $attribute, 'entity_id', null, 'left', $store->getId());                
                    $collection->joinAttribute($attribute . '_defaultValue', 'catalog_product/' . $attribute, 'entity_id', null, 'left', $adminStore);
                } else {
                    $collection->addAttributeToSelect($attribute);
                }
            }
            $collection->addStoreFilter($store);            
        } else {
            foreach($this->_getAttributesColumns() as $attribute => $data) {
                $collection->addAttributeToSelect($attribute);
            }
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {            
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
                'input_type' => 'hidden',
                'renderer'   => 'alekseon_ajaxifiedProductEdit/adminhtml_gridView_grid_renderer_input',
        ));

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));        
        
        foreach($this->_getAttributesColumns() as $attribute => $data) {
            $columnSettings = array(
                    'attribute'  => $data['attribute'],
                    'header'     => Mage::helper('catalog')->__($data['label']) . '<br/>' . $this->_getScope($data['scope']),
                    'index'      => $attribute,                    
                    'input_type' => $data['input'],
                    'scope'      => $data['scope'],
                    'options'    => $data['options'],
                    'renderer'   => $data['renderer'],
                    'editable'   => $data['editable'],
            );
            
            $columnSettings['type'] = $data['input'];
            if ($data['options']) {
                $columnSettings['type'] = 'options';
            }
            if ($data['input'] == 'price') {
                $columnSettings['currency_code'] = $this->_getStore()->getBaseCurrency()->getCode();
            }

            $this->addColumn($attribute, $columnSettings);
        }
        
        $this->addColumn('save_button',
            array(
                'header'   => '',
                'renderer' => 'alekseon_ajaxifiedProductEdit/adminhtml_gridView_grid_renderer_saveButton',
                'filter'   => false,
        ));        

        return parent::_prepareColumns();
    }

    protected function _getScope($scope)
    {
        if (is_null($scope)) {
            $scope = 0;
        }

        $scopes = array(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('catalog')->__('Store View'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('catalog')->__('Website'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('catalog')->__('Global'),
        );

        if ($scope!==false && array_key_exists($scope, $scopes)) {
            return '<span class="scope-label">[' . $scopes[$scope] . ']</span>';
        } else {
            return false;
        }
    }

    public function getRowUrl($row)
    {
        return false;
    }
    
    public function prepareRow($productId)
    {
        $this->_prepareColumns();
        $this->_prepareCollection(array($productId));

        return $this;
    }
}
