<?php 
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Block_Adminhtml_Sales_Order_View extends Mage_Core_Block_Template {
	protected $_order;
	public function __construct(){
		$this->_order = Mage::getModel('sales/order');
		if(Mage::app()->getRequest()->getParam('order_id')){
			$this->_order->load(Mage::app()->getRequest()->getParam('order_id'));
		}
	}
	public function getOrder(){
		return $this->_order;
	}
	public function getCities(){
		return Mage::helper('sy_novaposhta')->getCities();
	}
	public function getRecipientCityRef(){
		$city = $this->_order->getShippingAddress()->getCity();
		$city = Mage::helper('sy_novaposhta')->findCity($city);
		return @$city['Ref'];
	}
}