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
		$city = Mage::app()->getRequest()->getParam('value');
		$billing_address->setCity($city)->save();
		$shipping_address->setCity($city)->save();
		if($city){
			$city = $helper->findCity($city);
			if($city){
				// $weight = $quote->getShippingAddress()->getWeight();
				$weight = SY_Novaposhta_Helper_Data::getCartWeight();
				$cost = $quote->getSubtotal();
				$price[0] = $helper->getCost($senderCity, $city['Ref'], "WarehouseWarehouse", $weight, $cost);
                $price[1] = $helper->getCost($senderCity, $city['Ref'], "WarehouseDoors", $weight, $cost);
			}
		}
		$price[0] = Mage::helper('core')->currency($price[0], true, false);
		$price[1] = Mage::helper('core')->currency($price[1], true, false);
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$quote->getShippingAddress()->setCollectShippingRates(true);
		$quote->getShippingAddress()->collectShippingRates();
		$quote->setTotalsColl‌​ectedFlag(false); 
		$quote->collectTotals(); 
		$quote->save();
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(json_encode(array($price)));
	}
	public function warehouseAction(){
		$ref = Mage::app()->getRequest()->getParam('ref');
		$description = Mage::app()->getRequest()->getParam('description');
		Mage::getSingleton('checkout/session')->getQuote()->setNovaposhtaWarehouse($ref)->save();
		Mage::getSingleton('checkout/session')->getQuote()->setNovaposhtaDescription($description)->save();
	}
	public function streetAction(){
		$ref = Mage::app()->getRequest()->getParam('ref');
		$name = Mage::app()->getRequest()->getParam('name');
		Mage::getSingleton('checkout/session')->getQuote()->setNovaposhtaStreet($ref)->save();
		Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->setStreet($name)->save();
		Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->setStreet($name)->save();
	}
	public function houseAction(){
		if(Mage::app()->getRequest()->getParam('mode') == 'update'){
			Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()
			->setHouse(Mage::app()->getRequest()->getParam('value'))->save();
			Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()
			->setHouse(Mage::app()->getRequest()->getParam('value'))->save();
		}
		$response = array('house'=>Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getHouse());
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(json_encode($response));
	}
	public function flatAction(){
		if(Mage::app()->getRequest()->getParam('mode') == 'update'){
			Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()
			->setFlat(Mage::app()->getRequest()->getParam('value'))->save();
			Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()
			->setFlat(Mage::app()->getRequest()->getParam('value'))->save();
		}
		$response = array('flat'=>Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getFlat());
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(json_encode($response));
	}
	public function noteAction(){
		if(Mage::app()->getRequest()->getParam('mode') == 'update'){
			Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()
			->setNote(Mage::app()->getRequest()->getParam('value'))->save();
			Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()
			->setNote(Mage::app()->getRequest()->getParam('value'))->save();
		}
		$response = array('note'=>Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getNote());
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(json_encode($response));
	}
	public function citiesAction(){
		$layout = $this->getLayout();
		$block = $layout->createBlock('sy_novaposhta/options');
		$options = Mage::helper('sy_novaposhta')->getCities();
		$block->setOptions($options);
		$block->setValues(array('Description'));
		$block->setAttributes(array('ref'=>'Ref'));
		$block->setInners(array('Description'));
		if($selected = $this->getRequest()->getParam('selected')){
			$block->setSelected($selected);
		}
		elseif($this->getRequest()->getParam('area') != 'admin'){
			$block->setSelected(Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getCity());
		}
		$block->setTemplate('SY/novaposhta/options.phtml');
		echo $block->toHtml();
	}
	public function warehousesAction(){
		$layout = $this->getLayout();
		$block = $layout->createBlock('sy_novaposhta/options');
		if($ref = Mage::app()->getRequest()->getParam('ref')){
			$options = Mage::helper('sy_novaposhta')->findWarehouses($ref);
			$block->setOptions($options);
			$block->setAttributes(array('ref'=>'Ref'));
			$block->setValues(array('Description'));
			$block->setInners(array('Description'));
			if($selected = $this->getRequest()->getParam('selected')){
				$block->setSelected($selected);
			}
			// Session value only for non-admin area !
			elseif($this->getRequest()->getParam('area') != 'admin'){
				if($selected = Mage::getSingleton('checkout/session')->getQuote()->getNovaposhtaWarehouse()){
					$block->setSelected($selected);
				}
			}
		}
		$block->setTemplate('SY/novaposhta/options.phtml');
		echo $block->toHtml();
	}
	public function streetsAction(){
		$layout = $this->getLayout();
		$block = $layout->createBlock('sy_novaposhta/options');
		if($ref = Mage::app()->getRequest()->getParam('ref')){
			$options = Mage::helper('sy_novaposhta')->findStreets($ref);
			$block->setOptions($options);
			$block->setAttributes(array('ref'=>'Ref'));
			$block->setValues(array('Description'));
			$block->setInners(array('Description','StreetsType'));
			if($selected = $this->getRequest()->getParam('selected')){
				$block->setSelected($selected);
			}
			// Session value only for non-admin area !
			elseif($this->getRequest()->getParam('area') != 'admin'){
				if($selected = Mage::getSingleton('checkout/session')->getQuote()->getNovaposhtaStreet()){
					$block->setSelected($selected);
				}
			}
		}
		$block->setTemplate('SY/novaposhta/options.phtml');
		echo $block->toHtml();
	}
	public function intervalsAction(){
		$layout = $this->getLayout();
		$block = $layout->createBlock('sy_novaposhta/options');
		if($ref = $this->getRequest()->getParam('ref')){
			$options = Mage::helper('sy_novaposhta')->getTimeIntervals($ref);
			$block->setOptions($options);
			$block->setValues(array('Number'));
			$block->setInners(array('Start','End'));
			$block->setSelected($this->getRequest()->getParam('selected'));
		}
		$block->setTemplate('SY/novaposhta/options.phtml');
		echo $block->toHtml();
	}
}