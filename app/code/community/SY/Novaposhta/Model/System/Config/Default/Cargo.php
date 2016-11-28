<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Model_System_Config_Default_Cargo
{
    public function toOptionArray($isMultiselect = false)
    {
        $options = array(array('value'=>'','label'=>''));
        $helper = Mage::helper('sy_novaposhta');
        if((bool)$helper->getStoreConfig('api_key') !== false && 
            $helper->getStoreConfig('active') == "1"){
            $cargos = $helper->getCargoTypes();
            if(count($cargos)>0){
                foreach ($cargos as $cargo) {
                    $options[] = array('value'=>$cargo['Ref'],'label'=>$cargo['Description']);
                }
            }
        }
        return $options;
    }
}