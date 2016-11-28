<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Block_Adminhtml_Sales_Order_Tab 
extends Mage_Adminhtml_Block_Template
implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	public function _construct()
	{
		parent::_construct();
		
		$this->setTemplate('sy/novaposhta/sales/order/tab.phtml');
	}
    public function getTabLabel()
    {
    	return $this->__('Novaposhta');
    }
    public function getTabTitle()
    {
    	return $this->__('Novaposhta');
    }
    public function canShowTab()
    {
		if((bool)Mage::helper('sy_novaposhta')->getStoreConfig('api_key') !== false && 
			Mage::helper('sy_novaposhta')->getStoreConfig('active') == "1"){
			return true;
		}
    }
    public function isHidden()
    {
    	return false;
    }
	public function getTabClass()
	{
		return 'ajax';
	}
	public function getSkipGenerateContent()
	{
		return true;
	}
	public function getTabUrl()
	{
		return $this->getUrl('*/novaposhta_sync/tab', array('_current' => true));
	}
}