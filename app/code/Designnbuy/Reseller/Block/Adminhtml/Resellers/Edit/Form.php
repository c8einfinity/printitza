<?php
namespace Designnbuy\Reseller\Block\Adminhtml\Resellers\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    protected function _prepareForm()
    {
        $importId = $this->getRequest()->getParam('import_id');
        $resellerId = $this->getRequest()->getParam('reseller_id');
        $saveAction = $this->getUrl('*/*/save');
        if($importId){
            $saveAction = $this->getUrl('*/*/save', ['import_id'=>4, 'reseller_id'=>$resellerId]);
        }
        $form = $this->_formFactory->create(
            array(
                'data' => array(
                    'id' => 'edit_form',
                    //'action' => $this->getUrl('*/*/save'),
                    'action' => $saveAction,
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                )
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
