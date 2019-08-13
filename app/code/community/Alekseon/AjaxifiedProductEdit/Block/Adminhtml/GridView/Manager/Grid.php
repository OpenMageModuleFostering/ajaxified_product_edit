<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_GridView_Manager_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('alekseon_ajaxifiedProductEdit_gridView_grid');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {        
		$collection = Mage::getModel('alekseon_ajaxifiedProductEdit/gridView')->getCollection();
		$this->setCollection($collection);

        parent::_prepareCollection();	

        return $this;	
	}    

	protected function _prepareColumns()
    {
        $this->addColumn('label', 
			array(
				'header' => Mage::helper('alekseon_ajaxifiedProductEdit')->__('Label'),
				'index'  => 'label',
                'width'  => '1px',
            )
        );
        
        $this->addColumn('description', 
			array(
				'header' => Mage::helper('alekseon_ajaxifiedProductEdit')->__('Description'),
				'index'  => 'description',
            )
        );

        $this->addColumn('enable', 
			array(
				'header' => Mage::helper('alekseon_ajaxifiedProductEdit')->__('Enable'),
				'index'  => 'enable',
                'width'  => '1px',
                'type'   => 'options',
                'options'=> array(0 => $this->__('No'), 1 => $this->__('Yes')),
            )
        );                
        
        return parent::_prepareColumns();
    }

	public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }    
    
}