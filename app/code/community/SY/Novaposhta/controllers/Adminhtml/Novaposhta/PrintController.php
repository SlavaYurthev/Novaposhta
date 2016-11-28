<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Adminhtml_Novaposhta_PrintController extends Mage_Adminhtml_Controller_Action
{
    protected function getCollection($order_ids){
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->addAttributeToFilter('entity_id', array('in'=>$order_ids));
        $collection->addAttributeToFilter('novaposhta_ref', array('notnull'=>true));
        $collection->addAttributeToFilter('novaposhta_barcode', array('notnull'=>true));
        return $collection;
    }
    public function marksAction(){
        $order_ids = $this->getRequest()->getParam('order_ids');
        if(count($order_ids)>0){
            $collection = $this->getCollection($order_ids);
            if($collection->count()>0){
                $data = Mage::helper('sy_novaposhta')->getMarks($collection);
                if(count($data)>0){
                    foreach ($data as $key => $link) {
                        $this->_prepareDownloadResponse(Mage::helper('sy_novaposhta')->__('Marks').'.pdf', file_get_contents($link));
                    }
                }
            }
        }
    }
    public function ttnAction(){
        $order_ids = $this->getRequest()->getParam('order_ids');
        if(count($order_ids)>0){
            $collection = $this->getCollection($order_ids);
            if($collection->count()>0){
                $data = Mage::helper('sy_novaposhta')->getTtn($collection);
                if(count($data)>0){
                    foreach ($data as $key => $link) {
                        $this->_prepareDownloadResponse(Mage::helper('sy_novaposhta')->__('TTN').'.pdf', file_get_contents($link));
                    }
                }
            }
        }
    }
    public function markshtmlAction(){
        $order_ids = $this->getRequest()->getParam('order_ids');
        if(count($order_ids)>0){
            $collection = $this->getCollection($order_ids);
            if($collection->count()>0){
                $data = Mage::helper('sy_novaposhta')->getMarks($collection, "html_link");
                if(count($data)>0){
                    foreach ($data as $key => $link) {
                        $data = file_get_contents($link);
                        $data = str_replace('href="/', 'href="https://my.novaposhta.ua/', $data);
                        $data = str_replace('src="/', 'src="https://my.novaposhta.ua/', $data);
                        $this->_prepareDownloadResponse(Mage::helper('sy_novaposhta')->__('Marks').'.html', $data);
                    }
                }
            }
        }
    }
    public function ttnhtmlAction(){
        $order_ids = $this->getRequest()->getParam('order_ids');
        if(count($order_ids)>0){
            $collection = $this->getCollection($order_ids);
            if($collection->count()>0){
                $data = Mage::helper('sy_novaposhta')->getTtn($collection, "html_link");
                if(count($data)>0){
                    foreach ($data as $key => $link) {
                        $data = file_get_contents($link);
                        $data = str_replace('href="/', 'href="https://my.novaposhta.ua/', $data);
                        $data = str_replace('src="/', 'src="https://my.novaposhta.ua/', $data);
                        $this->_prepareDownloadResponse(Mage::helper('sy_novaposhta')->__('TTN').'.html', $data);
                    }
                }
            }
        }
    }
}