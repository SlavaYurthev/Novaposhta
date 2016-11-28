<?php
class SY_Novaposhta_Model_Api_Client
{
    protected $_client;
    protected function _getApiKey()
    {
        $key = Mage::helper('sy_novaposhta')->getStoreConfig('api_key');
        if (!trim($key)) {
            throw new Exception('No API key configured');
        }
        return $key;
    }
    protected function _getClient()
    {
        if (!$this->_client) {
            $this->_client = new NovaPoshta_Api2(
                $this->_getApiKey(),
                'ru',
                FALSE,
                'curl'
            );
        }
        return $this->_client;
    }
    public function getConnection(){
        return $this->_getClient();
    }
}
