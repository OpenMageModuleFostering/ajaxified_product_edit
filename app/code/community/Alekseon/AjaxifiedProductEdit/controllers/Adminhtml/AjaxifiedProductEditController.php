<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Adminhtml_AjaxifiedProductEditController extends Mage_Adminhtml_Controller_Action
{
    public function _initGridView()
    {
        $storeId = (int)$this->getRequest()->getParam('store', false);
        Mage::register('current_storeId', $storeId);
    
        $gridViewId = (int)$this->getRequest()->getParam('view', false);                
        $availableGridViews = Mage::helper('alekseon_ajaxifiedProductEdit')->getAvailableGridViews();
        
        if ($gridViewId) {
            foreach($availableGridViews as $gridView) {
                if ($gridView->getId() == $gridViewId) {
                    Mage::register('current_gridView', $gridView);
                    return $this;
                }
            }
        } else {
            $gridView = $availableGridViews->getFirstItem();
            Mage::register('current_gridView', $gridView);            
        }
        
        return $this;    
    }

    public function indexAction()
    {    
        if (!Mage::helper('alekseon_ajaxifiedProductEdit')->getAvailableGridViews()->count()) {
            if (Mage::getSingleton('admin/session')->isAllowed('system/alekseon_ajaxifiedProductEdit/managegridviews')) {
                $url = $this->getUrl('*/ajaxifiedProductEdit_manageGridViews/');
                $alekseonEmail = Mage::helper('alekseon_ajaxifiedProductEdit')->getAlekseonEmail();
                $errorMessage = Mage::helper('alekseon_ajaxifiedProductEdit')->__("Ajaxified Product Edit is unavailable, please edit your <a href=\"%s\">Grid Views</a> or contact with support <a href=\"mailto:%s\">%s</a>.", $url, $alekseonEmail,  $alekseonEmail);
            } else {
                $errorMessage = Mage::helper('alekseon_ajaxifiedProductEdit')->__('Ajaxified Product Edit is unavailable, please contact with administrator.');
            }
            $this->_getSession()->addError($errorMessage);
            $this->_redirectReferer();
            return;
        }
        
        $this->_initGridView();
        $currentGridView = Mage::helper('alekseon_ajaxifiedProductEdit')->getCurrentGridView();
        
        if (!$currentGridView) {
            $this->_forward('noRoute');
            return;
        }
    
        $this->loadLayout();
        $this->_setActiveMenu('catalog/products');
        $this->renderLayout();
    }

    public function saveRowAction()
    {
        $params = $this->getRequest()->getParams();
        $rowId = $params['rowId'];
        $productData = $params['product'][$rowId];
        $storeId = $params['store'];

        $product = Mage::getModel('catalog/product');
        if ($storeId) {
            $product->setStoreId($storeId);
        }
        
        $result = array();
        $result['rowId'] = $rowId;
        
        try {        
            $product->load($rowId);
            
            if (!$product->getId()) {
                Mage::throwException($this->__('Product Id is NULL.'));
            }
            
            foreach($productData as $attribute => $data) {
                if ($storeId && isset($data['default'])) {
                    $product->setData($attribute, false);
                } elseif (isset($data['value'])) {
                    $product->setData($attribute, $data['value']);
                }
            }
            if (!Mage::getStoreConfig('alekseon_ajaxifiedProductEdit/advanced/use_resource')) {
                $product->save();
            } else {
                $product->getResource()->save($product);
                if (Mage::helper('core')->isModuleEnabled('Enterprise_Logging')) {
                    Mage::getSingleton('enterprise_logging/processor')->modelActionAfter($product, 'save');
                }
            }

            if (Mage::getStoreConfig('alekseon_ajaxifiedProductEdit/advanced/refresh_row')) {
                $newFields = $this->_prepareRefreshedFields($rowId);
                foreach($newFields as $attribute => $html) {
                    $result['updateData']['field[' . $rowId . '][' . $attribute . ']'] = $html;
                }
            }

            $result['successMsg'] = $this->__('Save succesed');
        } catch(Exception $e) {
            Mage::log($e->getMessage(), null,'alekseon_ajaxifiedProductEdit.log');
            $result['errorMsg'] = $this->__('Save failed. Check logs for details.');            
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    protected function _prepareRefreshedFields($productId)
    {
        $this->_initGridView();
        $gridBlock = $this->getLayout()->createBlock('alekseon_ajaxifiedProductEdit/adminhtml_gridView_grid', 'gridRow');
        $gridBlock->prepareRow($productId);
        $this->loadLayout();

        $result = array();
        foreach($gridBlock->getCollection() as $_item) {
            foreach ($gridBlock->getColumns() as $_columnCode => $_column) {
                if (!$_column->getEditable()) {
                    continue;
                }
                $rowField = $_column->getRowField($_item);
                if ($rowField) {
                    $result[$_columnCode] = $rowField;
                } else {
                    $result[$_columnCode] = '&nbsp;';
                }
            }
        }
        
        return $result;
    }
}