<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Adminhtml_AjaxifiedProductEdit_ManageGridViewsController extends Mage_Adminhtml_Controller_Action
{
    public function _initGridView()
    {
        $gridViewId = (int) $this->getRequest()->getParam('id');
        $gridView = Mage::getModel('alekseon_ajaxifiedProductEdit/gridView');

        if ($gridViewId) {
            $gridView->load($gridViewId);
        }

        Mage::register('current_gridView', $gridView);
        return $this;    
    }

    public function indexAction()
    {
        $this->loadLayout();

        $this->_addContent(
            $this->getLayout()->createBlock('alekseon_ajaxifiedProductEdit/adminhtml_gridView_manager', 'manageGridView')
        );
        
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_initGridView();
    
        $this->loadLayout();
        $this->renderLayout();
    }    

    public function deleteAction()
    {
        $this->_initGridView();
        $gridView = Mage::registry('current_gridView');    
        
        try {
            $gridView->delete();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->getResponse()->setRedirect($this->getUrl('*/ajaxifiedProductEdit_manageGridViews'));
            return;
        }
        
        $this->_getSession()->addSuccess(
            Mage::helper('alekseon_ajaxifiedProductEdit')->__('Grid View set has been removed.')
        );
        
        $this->getResponse()->setRedirect($this->getUrl('*/ajaxifiedProductEdit_manageGridViews'));
    }
    
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();

        if ($data) {
            $this->_initGridView();
            $gridView = Mage::registry('current_gridView');
            try {
                $gridView->setData($data);

                if (isset($data['user_roles'])) {
                    $gridView->setUserRoles(serialize($data['user_roles']));
                }

                if (isset($data['columns'])) {
                    $attributes = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['columns']['attributes']);
                    $gridView->setAttributesData($attributes);
                }

                $gridView->save();
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/ajaxifiedProductEdit_manageGridViews/edit', array('id' => $gridView->getId())));
                return;
            }
        }
        
        $this->_getSession()->addSuccess(
            Mage::helper('alekseon_ajaxifiedProductEdit')->__('Grid View set has been saved.')
        );
        
        $this->getResponse()->setRedirect($this->getUrl('*/ajaxifiedProductEdit_manageGridViews'));
    }
    
    public function manageColumnsAction()
    {
        $this->_initGridView();

        $this->loadLayout();
        $this->getLayout()->getBlock('grid_view_edit_tabs_manage_columns_attributes')
            ->setSelectedAttributesIds($this->getRequest()->getPost('gridview_selected_attributes', null));
        $this->renderLayout();
    }
    
    public function manageColumnsGridAction()
    {
        $this->_initGridView();

        $this->loadLayout();
        $this->getLayout()->getBlock('grid_view_edit_tabs_manage_columns_attributes')
            ->setSelectedAttributesIds($this->getRequest()->getPost('gridview_selected_attributes', array()));
        $this->renderLayout();
    }
}