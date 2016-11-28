<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Model_System_Config_City
{
    public function toOptionArray($isMultiselect = false)
    {
        $options = array(array('value'=>'','label'=>''));
        $helper = Mage::helper('sy_novaposhta');
        if((bool)$helper->getStoreConfig('api_key') !== false && 
            $helper->getStoreConfig('active') == "1"){
            $cities = $helper->getCities();
            if(count($cities)>0){
                foreach ($cities as $city) {
                    $options[] = array('value'=>$city['Ref'],'label'=>$city['Description']);
                }
            }
        }
        return $options;
    }
}