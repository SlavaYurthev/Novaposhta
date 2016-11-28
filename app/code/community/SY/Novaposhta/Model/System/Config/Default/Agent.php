<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Model_System_Config_Default_Agent
{
    public function toOptionArray($isMultiselect = false)
    {
        $options = array(array('value'=>'','label'=>''));
        $helper = Mage::helper('sy_novaposhta');
        if((bool)$helper->getStoreConfig('api_key') !== false && 
            $helper->getStoreConfig('active') == "1"){
            $agents = $helper->getContragents();
            if(count($agents)>0){
                foreach ($agents as $agent) {
                    $options[] = array('value'=>$agent['Ref'],'label'=>$agent['Description']);
                }
            }
        }
        return $options;
    }
}