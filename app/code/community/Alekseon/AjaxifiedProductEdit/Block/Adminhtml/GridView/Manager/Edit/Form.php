<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_GridView_Manager_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getActionUrl(),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getActionUrl()
    {
        return $this->getUrl('adminhtml/ajaxifiedProductEdit_manageGridViews/save');
    }
}
