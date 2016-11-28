<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Model_System_Config_Default_Payment
{
    public function toOptionArray($isMultiselect = false)
    {
        $options = array(array('value'=>'','label'=>''));
        $helper = Mage::helper('sy_novaposhta');
        if((bool)$helper->getStoreConfig('api_key') !== false && 
            $helper->getStoreConfig('active') == "1"){
            $payments = $helper->getPaymentForms();
            if(count($payments)>0){
                foreach ($payments as $payment) {
                    $options[] = array('value'=>$payment['Ref'],'label'=>$payment['Description']);
                }
            }
        }
        return $options;
    }
}