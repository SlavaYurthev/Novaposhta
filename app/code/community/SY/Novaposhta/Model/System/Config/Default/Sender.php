<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Model_System_Config_Default_Sender
{
    public function toOptionArray($isMultiselect = false)
    {
        $options = array(array('value'=>'','label'=>''));
        $helper = Mage::helper('sy_novaposhta');
        if((bool)$helper->getStoreConfig('api_key') !== false && 
            $helper->getStoreConfig('active') == "1"){
            $senders = $helper->getSenders();
            if(count($senders)>0){
                foreach ($senders as $sender) {
                    $options[] = array('value'=>$sender['Ref'],'label'=>$sender['Description']);
                }
            }
        }
        return $options;
    }
}