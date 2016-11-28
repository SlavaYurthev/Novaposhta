<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Model_Resource_Cities extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('sy_novaposhta/sy_novaposhta_cities', 'id');
    }
}