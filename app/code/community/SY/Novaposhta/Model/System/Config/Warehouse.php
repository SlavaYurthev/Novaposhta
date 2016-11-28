<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Model_System_Config_Warehouse
{
    public function toOptionArray($isMultiselect = false)
    {
        $options = array(array('value'=>'','label'=>''));
        $helper = Mage::helper('sy_novaposhta');
        if((bool)$helper->getStoreConfig('api_key') !== false && 
            $helper->getStoreConfig('active') == "1"){
            if($helper->getStoreConfig('sender_city')){
                $warehouses = $helper->findWarehouses($helper->getStoreConfig('sender_city'));
                if(count($warehouses)>0){
                    foreach ($warehouses as $warehouse) {
                        $options[] = array('value'=>$warehouse['Ref'],'label'=>$warehouse['Description']);
                    }
                }
            }
        }
        return $options;
    }
}