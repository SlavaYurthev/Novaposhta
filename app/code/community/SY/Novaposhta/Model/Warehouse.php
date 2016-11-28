<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Model_Warehouse extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('sy_novaposhta/warehouse');
    }
    public function getCity()
    {
        return Mage::getModel('sy_novaposhta/city')->load($this->getData('city_id'));
    }
}
