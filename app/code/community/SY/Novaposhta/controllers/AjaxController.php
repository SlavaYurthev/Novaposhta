<?php
ini_set('display_errors', 1);
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_AjaxController extends Mage_Core_Controller_Front_Action {
	public function cityAction(){
		$quote = Mage::getModel("checkout/session")->getQuote();
		$billing_address = $quote->getBillingAddress();
		$shipping_address = $quote->getShippingAddress();
		$helper = Mage::helper('sy_novaposhta');
		$senderCity = Mage::app()->getWebsite()->getConfig('carriers/sy_novaposhta/sender_city');
		$price = array(0, 0);
		if($city = Mage::app()->getRequest()->getParam('value')){
			$billing_address->setCity($city)->save();
			$shipping_address->setCity($city)->save();
			$city = $helper->findCity($city);
			if($city){
				// $weight = $quote->getShippingAddress()->getWeight();
				$weight = SY_Novaposhta_Helper_Data::getCartWeight();
				$cost = $quote->getSubtotal();
				$price[0] = $helper->getCost($senderCity, $city['Ref'], "WarehouseWarehouse", $weight, $cost);
                $price[1] = $helper->getCost($senderCity, $city['Ref'], "WarehouseDoors", $weight, $cost);
			}
		}
		else{
			$billing_address->setCity(false)->save();
			$shipping_address->setCity(false)->save();
		}
		$price[0] = Mage::helper('core')->currency($price[0], true, false);
		$price[1] = Mage::helper('core')->currency($price[1], true, false);
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(json_encode(array($price)));
	}
	public function warehouseAction(){
		if($ref = Mage::app()->getRequest()->getParam('ref')){
			Mage::getSingleton('checkout/session')->getQuote()->setNovaposhtaWarehouse($ref)->save();
		}
		else{
			Mage::getSingleton('checkout/session')->getQuote()->setNovaposhtaWarehouse(false)->save();
		}
		if($description = Mage::app()->getRequest()->getParam('description')){
			Mage::getSingleton('checkout/session')->getQuote()->setNovaposhtaDescription($description)->save();
		}
		else{
			Mage::getSingleton('checkout/session')->getQuote()->setNovaposhtaDescription(false)->save();
		}
	}
	public function citiesAction(){
		$cities = Mage::helper('sy_novaposhta')->getCities();
		$html = "<option></option>";
		if(count($cities)>0){
			foreach ($cities as $city) {
				$selected = "";
				$customer = Mage::getSingleton('checkout/session');
				if($city['Description'] == $customer->getQuote()->getShippingAddress()->getCity() || 
					$city['DescriptionRu'] == $customer->getQuote()->getShippingAddress()->getCity()){
					$selected = ' selected="selected" ';
				}
				$html .= "<option ref='".$city['Ref']."' ".'value="'.htmlentities($city['Description']).'"'.$selected.">".$city['Description']."</option>";
			}
		}
		print_r($html);
	}
	public function warehousesAction(){
		$warehouses = Mage::helper('sy_novaposhta')->findWarehouses(Mage::app()->getRequest()->getParam('ref'));
		$html = "<option></option>";
		if(count($warehouses)>0 && Mage::app()->getRequest()->getParam('ref')){
			foreach ($warehouses as $warehouse) {
				$html .= "<option value='".htmlentities($warehouse['Description'])."'";
				if(Mage::app()->getRequest()->getParam('warehouse') == $warehouse['Ref'] || 
					urldecode(Mage::app()->getRequest()->getParam('warehouse')) == $warehouse['Description'] ||
					Mage::getSingleton('checkout/session')->getQuote()->getNovaposhtaWarehouse() == $warehouse['Ref']){
					$html .= 'selected="selected"';
				}
				$html .= " ref='".htmlentities($warehouse['Ref'])."'";
				$html .= ">".$warehouse['Description']."</option>";
			}
		}
		echo $html;
	}
	public function intervalsAction(){
		$intervals = Mage::helper('sy_novaposhta')->getTimeIntervals(Mage::app()->getRequest()->getParam('ref'));
		$html = "<option></option>";
		if(count($intervals)>0){
			foreach ($intervals as $interval) {
				$html .= "<option value='".$interval['Number']."'";
				if(Mage::app()->getRequest()->getParam('selected') == $interval['Number']){
					$html .= " selected='selected' ";
				}
				$html .= ">".$interval['Start']."-".$interval['End']."</option>";
			}
		}
		echo $html;
	}
}