<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_GridView_Manager_Edit_Tabs_General extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return Mage::helper('alekseon_ajaxifiedProductEdit')->__('General');
    }

    public function getTabTitle()
    {
        return Mage::helper('alekseon_ajaxifiedProductEdit')->__('General');
    }
    
    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $gridView = Mage::registry('current_gridView');
        $data = $gridView->getData();
    
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('alekseon_ajaxifiedProductEdit')->__('General')));

        if ($gridView->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
            ));
        }
        
        $fieldset->addField('label', 'text', array(
            'label'     => Mage::helper('alekseon_ajaxifiedProductEdit')->__('Label'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'label',
        ));
        
        $fieldset->addField('description', 'textarea', array(
            'label'     => Mage::helper('alekseon_ajaxifiedProductEdit')->__('Description'),
            'name'      => 'description',
        ));
        
        $fieldset->addField('enable', 'select', array(
            'label'     => Mage::helper('alekseon_ajaxifiedProductEdit')->__('Enable'),
            'name'      => 'enable',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $fieldset = $form->addFieldset('access', array('legend'=>Mage::helper('alekseon_ajaxifiedProductEdit')->__('Access')));

        $fieldset->addField('user_roles', 'multiselect', array(
            'label'     => Mage::helper('alekseon_ajaxifiedProductEdit')->__('User Roles'),
            'name'      => 'user_roles',
            'values'    => Mage::getSingleton('alekseon_ajaxifiedProductEdit/system_config_source_userRole')->toOptionArray(),
        ));

        if(isset($data['user_roles']) && unserialize($data['user_roles'])){
            $data['user_roles'] = unserialize($data['user_roles']);
        }

        $form->setValues($data);

        return parent::_prepareForm();
    }
    
    protected function _getAttributesColumns()
    {
        $gridView = Mage::registry('current_gridView');
        $attributes = array();

        foreach(Mage::helper('alekseon_ajaxifiedProductEdit')->getStaticFields() as $fieldCode => $fieldData) {
            $attributes[$fieldCode] = array(
                'code'     => $fieldCode,
                'label'    => $fieldData['label'],
                'editable' => false,
                'position' => 0,
            );
        }

        if(unserialize($gridView->getAttributes())){
            foreach(unserialize($gridView->getAttributes()) as $attributeCode => $attributeData) {
                if (isset($attributes[$attributeCode])) {
                    $attributes[$attributeCode]['position'] = $attributeData['position'];
                } else {
                    $attributes[$attributeCode] = array(
                        'code'     => $attributeCode,
                        'editable' => false,
                        'position' => $attributeData['position'],
                    );
                }
            }
        }

        if(unserialize($gridView->getEditableAttributes())){
            foreach(unserialize($gridView->getEditableAttributes()) as $attributeCode => $attributeData) {
                $attributes[$attributeCode] = array(
                    'code'     => $attributeCode,
                    'editable' => true,
                    'position' => $attributeData['position'],
                );
            }
        }


        
        $productAttributes = Mage::getSingleton('alekseon_ajaxifiedProductEdit/system_config_source_productAttributes')->toOptionArray();
        foreach($productAttributes as $attributeData) {
            $attributeCode = $attributeData['value'];
            if (isset($attributes[$attributeCode])) {
                $attributes[$attributeCode]['label'] = $attributeData['label'];
            }
        }
        
        usort($attributes, array($this,'_compareAttributes'));
        return $attributes;
    }
    
    protected function _compareAttributes($option1, $option2)
    {
        return (int)$option1['position'] > (int)$option2['position'];
    }
}
