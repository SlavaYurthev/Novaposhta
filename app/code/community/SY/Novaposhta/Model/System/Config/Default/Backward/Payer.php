<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Model_System_Config_Default_Backward_Payer
{
    public function toOptionArray($isMultiselect = false)
    {
        $options = array(array('value'=>'','label'=>''));
        $helper = Mage::helper('sy_novaposhta');
        if((bool)$helper->getStoreConfig('api_key') !== false && 
            $helper->getStoreConfig('active') == "1"){
            $options[] = array('value'=>'Sender','label'=>$helper->__('Sender'));
            $options[] = array('value'=>'Recipient','label'=>$helper->__('Recipient'));
        }
        return $options;
    }
}