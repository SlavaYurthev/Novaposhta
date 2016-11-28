<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Adminhtml_Novaposhta_RegisterController extends Mage_Adminhtml_Controller_Action
{
    protected function getCollection($order_ids){
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->addAttributeToFilter('entity_id', array('in'=>$order_ids));
        $collection->addAttributeToFilter('novaposhta_ref', array('notnull'=>true));
        $collection->addAttributeToFilter('novaposhta_barcode', array('notnull'=>true));
        return $collection;
    }
    public function addAction(){
        $order_ids = $this->getRequest()->getParam('order_ids');
        if(count($order_ids)>0){
            $collection = $this->getCollection($order_ids);
            if($collection->count()>0){
                // непонятная ошибка не описанная в списке ошибок новой почты
                Mage::helper('sy_novaposhta')->addToRegister($collection);
                // по-этому делаем повторное добавление
                Mage::helper('sy_novaposhta')->addToRegister($collection);
            }
        }
        $this->_redirect('adminhtml/sales_order/index');
        return $this;
    }
    public function deleteAction(){
        $order_ids = $this->getRequest()->getParam('order_ids');
        if(count($order_ids)>0){
            $collection = $this->getCollection($order_ids);
            if($collection->count()>0){
                Mage::helper('sy_novaposhta')->deleteFromRegister($collection);
            }
        }
        $this->_redirect('adminhtml/sales_order/index');
        return $this;
    }
}