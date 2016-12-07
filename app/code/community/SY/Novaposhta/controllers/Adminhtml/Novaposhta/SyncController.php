<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Adminhtml_Novaposhta_SyncController extends Mage_Adminhtml_Controller_Action
{
    public function deleteAction(){
        $order = Mage::getModel('sales/order')->load(Mage::app()->getRequest()->getParam('order_id'));
        if($order->getId() && $order->getNovaposhtaRef()){
            Mage::helper('sy_novaposhta')->deleteShipp($order);
        }
        $this->_redirect('adminhtml/sales_order/view', array('order_id' => Mage::app()->getRequest()->getParam('order_id')));
        return $this;
    }
    public function updateAction(){
        if($this->_validateFormKey()) {
            $params = Mage::app()->getRequest()->getParams();
            $order = Mage::getModel('sales/order')->load(Mage::app()->getRequest()->getParam('order_id'));
            if($order->getId()){
                $order->setNovaposhtaFormData(json_encode($params));
                try {
                    $order->save();
                } catch (Exception $e) {}
            }
            // Zend_Debug::dump($params);
            // exit;
            // if($params['info']['ServiceType'] == "WarehouseDoors" || $params['info']['ServiceType'] == "DoorsDoors"){
            //     $params['recepient']['Street'];
            // }
            $params['recepient']['Region'] = "";
            $params['recepient']['Phone'] = preg_replace("/[^0-9]/", '', $params['recepient']['Phone']);
            foreach (range(1, $params['info']['SeatsAmount']) as $key => $value) {
                $params['info']['OptionsSeat'][$key]['weight'] = @($params['info']["Weight"]/$params['info']['SeatsAmount']);
                if($params['info']['VolumeGeneral']){
                    $params['info']['OptionsSeat'][$key]['volumetricVolume'] = @($params['info']['VolumeGeneral']/$params['info']['SeatsAmount']);
                }
                if($params['info']['Width']){
                    $params['info']['OptionsSeat'][$key]['volumetricWidth'] = @($params['info']['Width']/$params['info']['SeatsAmount']);
                }
                if($params['info']['Height']){
                    $params['info']['OptionsSeat'][$key]['volumetricLength'] = @($params['info']['Height']/$params['info']['SeatsAmount']);
                }
                if($params['info']['Length']){
                    $params['info']['OptionsSeat'][$key]['volumetricHeight'] = @($params['info']['Length']/$params['info']['SeatsAmount']);
                }
            }
            $config[0] = array_merge((array)$params['sender'],(array)$params['contragent']);
            $config[1] = (array)$params['recepient'];
            $config[2] = (array)$params['info'];
            $config[0]['SenderAddress'] = Mage::app()->getWebsite()->getConfig('carriers/sy_novaposhta/sender_warehouse');
            try {
                Mage::helper('sy_novaposhta')->sent($config, Mage::app()->getRequest()->getParam('order_id'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }   
        $this->_redirect('adminhtml/sales_order/view', array('order_id' => Mage::app()->getRequest()->getParam('order_id')));
        return $this;
    }
    public function tabAction(){
    	echo Mage::app()->getLayout()->createBlock('sy_novaposhta/adminhtml_sales_order_view')->setTemplate('sy/novaposhta/sales/order/tab.phtml')->toHtml();
    }
}
