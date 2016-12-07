<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Helper_Data extends Mage_Core_Helper_Data {
	public function getStoreConfig($key){
		return Mage::getStoreConfig('carriers/sy_novaposhta/'.$key);
	}
	public function getCities($api = false){
		if($api == true || $this->getStoreConfig('on_air') == "1"){
            $cities = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getCities(0);
            if(isset($cities['data'])){
                return $cities['data'];
            }
        }
        else{
            $cities = Mage::getModel('sy_novaposhta/cities')->getCollection();
            if($cities->count()>0){
                return $cities->toArray()['items'];
            }
        }
	}
	public function findCity($city, $api = false){
        if($api == true || $this->getStoreConfig('on_air') == "1"){
    		$city = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getCities(0,$city);
    		if(isset($city['data']) && isset($city['data'][0])){
    			return $city['data'][0];
    		}
        }
        else{
            $cities = Mage::getModel('sy_novaposhta/cities')->getCollection();
            $cities->addFieldToFilter(array('Description','DescriptionRu'), array($city,$city));
            if($cities->count()>0){
                return $cities->getFirstItem()->getData();
            }
        }
	}
	public function findWarehouses($cityRef, $api = false){
		$data = array();
        if($api == true || $this->getStoreConfig('on_air') == "1"){
    		$warehouses = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getWarehouses($cityRef);
    		if(isset($warehouses['data']) && count($warehouses['data'])>0){
    			$data = array_merge($data, $warehouses['data']);
    		}
        }
        else{
            $warehouses = Mage::getModel('sy_novaposhta/warehouses')->getCollection();
            $warehouses->addFieldToFilter('CityRef',$cityRef);
            $warehouses->getSelect()->order('CAST(Number as UNSIGNED) asc');
            if($warehouses->count()>0){
                $data = array_merge($data, $warehouses->toArray()['items']);
            }
        }
		return $data;
	}
    public function findStreets($cityRef, $api = false){
        $data = array();
        $streets = false;
        $page = 1;
        if($api == true || $this->getStoreConfig('on_air') == "1"){
            do {
                $streets = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getStreet($cityRef, '', $page);
                if(isset($streets['data']) && count($streets['data'])>0){
                    $data = array_merge($data, $streets['data']);
                    $page++;
                }
                else{
                    $streets = false;
                }
            } while ($streets);
        }
        else{
            $streets = Mage::getModel('sy_novaposhta/streets')->getCollection();
            $streets->addFieldToFilter('CityRef',$cityRef);
            if($streets->count()>0){
                $data = array_merge($data, $streets->toArray()['items']);
            }
        }
        return $data;
    }
	public function getCost($citySender, $cityRecipient, $serviceType, $weight, $cost){
        $default = 0;
		$response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getDocumentPrice($citySender, $cityRecipient, $serviceType, $weight, $cost);
		if(isset($response['data']) && isset($response['data'][0])){
            $default = $response['data'][0]['Cost'];
            $rates = Mage::getModel('directory/currency')->getCurrencyRates(Mage::app()->getStore()->getBaseCurrencyCode(), array('UAH'));
            // Если гривна не базовая валюта то для неё будет курс по которому мы посчитаем базовый эквивалент
            if(array_key_exists('UAH', $rates)){
                $default = $default / $rates['UAH'];
                $default = round($default, 2, PHP_ROUND_HALF_UP); // Magento type
            }
        }
        return $default;
	}
	public function getSenderCity(){
		$sender = $this->getSender();
		if($sender){
			return $sender['City'];
		}
	}
	public function getSenders(){
		$response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getCounterparties('Sender', 1, '', '');
		if(isset($response['data'])){
			return $response['data'];
		}
	}
	public function getSender(){
		if($this->getSenders()){
			return $this->getSenders()[0];
		}
	}
	public function getContragents(){
        $stack = array();
        if(count($this->getSenders()>0)){
            foreach ($this->getSenders() as $sender) {
                $agents = @$this->getAgents($sender['Ref']);
                if(count($agents)>0){
                    foreach ($agents as $agent) {
                        $stack[] = $agent;
                    }
                }
            }
        }
        return $stack;
    }
    public function getAgents($ref){
        $response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getCounterpartyContactPersons($ref);
        if (isset($response['success']) && $response['success'] == true) {
        	return $response['data'];
        }
    }
    public function getServiceTypes(){
    	$response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getServiceTypes();
        if (isset($response['success']) && $response['success'] == true) {
            // В процессе подключения адрессных доставок адрес в качестве отправления имеет низкий приоритет
            // и поэтому пока задисейблен и будет как только так сразу или по запросу
            if(count($response['data'])>0){
                foreach ($response['data'] as $key => $value) {
                    if($value['Ref'] == 'DoorsWarehouse' || $value['Ref'] == 'DoorsDoors'){
                        unset($response['data'][$key]);
                    }
                }
            }
        	return $response['data'];
        }
    }
    public function getPaymentForms(){
    	$response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getPaymentForms();
        if (isset($response['success']) && $response['success'] == true) {
        	return $response['data'];
        }
    }
    public function getBackwardDeliveryCargoTypes(){
        $response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getBackwardDeliveryCargoTypes();
        if (isset($response['success']) && $response['success'] == true) {
            return $response['data'];
        }
    }
    public function getCargoTypes(){
    	$response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getCargoTypes();
        if (isset($response['success']) && $response['success'] == true) {
        	return $response['data'];
        }
    }
    public function getTimeIntervals($ref){
    	$response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getTimeIntervals($ref);
        if (isset($response['success']) && $response['success'] == true) {
        	return $response['data'];
        }
    }
    public function completeOrder($order){
    	$resource = Mage::getSingleton('core/resource');
        $write = $resource->getConnection('core_write');
        $status = Mage_Sales_Model_Order::STATE_COMPLETE;
        $id = $order->getId();
        $write->query("UPDATE sales_flat_order_grid SET `status` = '{$status}' WHERE `entity_id`='{$id}'");
        $write->query("UPDATE sales_flat_order SET `state` = '{$status}', `status` = '{$status}' WHERE `entity_id`='{$id}'");
    }
    public function updateStatus($order){
    	$info = $this->getInfo($order);
    	if($info && isset($info['StateName'])){
    		$order->setNovaposhtaStatus($info['StateName']);
    		$order->save();
    		if($info['StateName'] == $this->getStoreConfig('complete_status')){
    			$this->completeOrder($order);
    		}
    	}
    }
    public function getInfo($order){
    	$response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->getDocument($order->getNovaposhtaRef());
        if (isset($response['success']) && $response['success'] == true && isset($response['data'][0])) {
        	return $response['data'][0];
        }
    }
    public function sent($config, $_orderId){
    	$_order = Mage::getModel('sales/order')->load($_orderId);
        $response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->newInternetDocument($config[0],$config[1],$config[2],$_order->getNovaposhtaRef());
        if (!isset($response['success']) || $response['success'] != true) {
            foreach ($response['errors'] as $error) {
                Mage::getSingleton('adminhtml/session')->addError($error);
            }
        }
        else{
            $msg = Mage::helper('sy_novaposhta')->__('Successfully Sent, declaration №%s',$response['data'][0]['IntDocNumber']);
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);
            $_order->setNovaposhtaBarcode($response['data'][0]['IntDocNumber']);
            $_order->setNovaposhtaRef($response['data'][0]['Ref']);
            $_order->save();
            $this->updateStatus($_order);
            try {
                $this->toShipp($_order,$response['data'][0]['IntDocNumber']);
            } catch (Exception $e) {}
        }
    }
    public function toShipp($order, $barcode){
        $api = new Mage_Sales_Model_Order_Shipment_Api;
        $shipmentIncrementId = $api->create($order->getIncrementId());
        $api->addTrack(
                    $shipmentIncrementId,
                    'novaposhta',
                    $order->getData('shipping_description'),
                    $barcode
                );
        $api->addComment($shipmentIncrementId, $order->getData('shipping_description')." ".$this->__("Declaration").' №'.$barcode,$order->getData('customer_email'),$order->getData('customer_email'));
    }
    public function deleteShipp($order){
    	$response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->deleteInternetDocument($order->getNovaposhtaRef());
        if (!isset($response['success']) || $response['success'] != true) {
            foreach ($response['errors'] as $error) {
                Mage::getSingleton('adminhtml/session')->addError($error);
            }
        }
        else{
	    	umask(0);
			Mage::app('default');
			Mage::register('isSecureArea', 1);
			$msg = Mage::helper('sy_novaposhta')->__('Declaration №%s, canceled',$order->getNovaposhtaBarcode());
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);
			$shipments = $order->getShipmentsCollection();
			foreach ($shipments as $shipment){
			    $shipment->delete();
			}
			$items = $order->getAllVisibleItems();
			foreach($items as $i){
			   $i->setQtyShipped(0);
			   $i->save();
			}
			$api = new Mage_Sales_Model_Order_Api;
			$api->addComment($order->getIncrementId(),Mage_Sales_Model_Order::STATE_PROCESSING,$msg,true);
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
			$order->setNovaposhtaBarcode(NULL);
			$order->setNovaposhtaStatus(NULL);
			$order->setNovaposhtaRef(NULL);
            $order->setNovaposhtaFormData(NULL);
			$order->save();
		}
    }
    public function addToRegister($collection){
        if($collection->count()>0){
            $refs = $collection->getColumnValues('novaposhta_ref');
            $response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->insertDocuments($refs);
            $sheets = array();
            if (isset($response['success']) && $response['success'] == true) {
                if(count($response['data'])>0){
                    foreach ($response['data'] as $item) {
                        if(isset($item['Data']['Errors']) && count($item['Data']['Errors'])>0){
                            foreach ($item['Data']['Errors'] as $error) {
                                try {
                                    // @$msg = $this->__('№'.$error['Number'].': '.$error['Error']);
                                    $sheets[$error['Ref']] = $error['ScanSheetNumber'];
                                    // @Mage::getSingleton('adminhtml/session')->addError($msg);
                                } catch (Exception $e) {
                                    // @Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                                }
                            }
                        }
                        elseif(isset($item['Data']['Success']) && count($item['Data']['Success'])>0){
                            foreach ($item['Data']['Success'] as $success) {
                                try {
                                    // @$msg = $this->__('№'.$success['Number'].': '.$success['Success']);
                                    $sheets[$success['Ref']] = $success['ScanSheetNumber'];
                                    // @Mage::getSingleton('adminhtml/session')->addSuccess($msg);
                                } catch (Exception $e) {
                                    // @Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                                }
                            }
                        }
                    }
                }
            }
            foreach ($collection as $item) {
                if(array_key_exists($item->getNovaposhtaRef(), $sheets)){
                    $item->setNovaposhtaRegister($sheets[$item->getNovaposhtaRef()]);
                    try {
                        $item->save();
                    } catch (Exception $e) {}
                }
            }
        }
    }
    public function deleteFromRegister($collection){
        if($collection->count()>0){
            $refs = $collection->getColumnValues('novaposhta_ref');
            $response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->removeDocuments($refs);
            Zend_Debug::dump($response);
            // Неизвестная ошибка - позже разберёмся...
        }
    }
    public function getMarks($collection, $type = "pdf_link"){
        if($collection->count()>0){
            $refs = $collection->getColumnValues('novaposhta_barcode');
            $response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->printMarkings($refs, $type);
            if(isset($response['data']) && count($response['data'])>0){
                return $response['data'];
            }
        }
    }
    public function getTtn($collection, $type = "pdf_link"){
        if($collection->count()>0){
            $refs = $collection->getColumnValues('novaposhta_barcode');
            $response = Mage::getSingleton('sy_novaposhta/api_client')->getConnection()->printDocument($refs, $type);
            if(isset($response['data']) && count($response['data'])>0){
                return $response['data'];
            }
        }
    }
    public static function getCartWeight(){
        $weight = Mage::getSingleton('checkout/session')
              ->getQuote()
              ->getShippingAddress()
              ->getWeight();
        if($weight < 0.05){
            $weight = 0.05;
        }
        return $weight;
    }
}