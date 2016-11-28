<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Model_Carrier_Novaposhta
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface{
    protected $_code = 'sy_novaposhta';
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $helper = Mage::helper('sy_novaposhta');
        $senderCity = Mage::app()->getWebsite()->getConfig('carriers/sy_novaposhta/sender_city');
        if ((bool)$helper->getStoreConfig('api_key') !== false && 
            $helper->getStoreConfig('active') == "1") {
            $result = Mage::getModel('shipping/rate_result');
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            // Устанавливаем по-умолчанию тип Склад-Склад если он ещё не установлен
            if(!$quote->getNovaposhtaType()){
                $quote->setNovaposhtaType('WarehouseWarehouse');
            }
            $price = array(0,0);
            // Всегда когда есть возможность устанавливаем город из системного запроса а не из корзины
            if($request->getDestCity()){
                $quote->getShippingAddress()->setCity($request->getDestCity());
            }
            // Если есть город получаем его Ref
            if($city = $quote->getShippingAddress()->getCity()){
                $city = $helper->findCity($city);
                if($city){
                    $quote->setNovaposhtaCity($city['Ref']);
                    // Если такой город есть в базе НовойПочты обновляем цену доставки
                    // $weight = $request->getPackageWeight();
                    $weight = $this->getCartWeight();
                    $cost = $request->getPackageValue();
                    $cost = Mage::helper('core')->currency($cost, false, false);
                    $price[0] = $helper->getCost($senderCity, $city['Ref'], "WarehouseWarehouse", $weight, $cost);
                    $price[1] = $helper->getCost($senderCity, $city['Ref'], "WarehouseDoors", $weight, $cost);
                    if(Mage::app()->getStore()->isAdmin()){
                        $warehouses = $helper->findWarehouses($city['Ref']);
                        if(count($warehouses)>0){
                            foreach ($warehouses as $warehouse) {
                                $method = Mage::getModel('shipping/rate_result_method');
                                $method->setCarrier($this->_code)
                                    ->setCarrierTitle($this->getConfigData('name'))
                                    ->setMethod('type_WarehouseWarehouse_'.$warehouse['Ref'])
                                    ->setMethodTitle($warehouse['Description'])
                                    ->setPrice($price[0])
                                    ->setCost($price[0]);

                                $result->append($method);
                                unset($method);
                            }
                        }
                    }
                }
            }
            
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier($this->_code)
                ->setCarrierTitle($this->getConfigData('name'))
                ->setMethod('type_WarehouseDoors')
                ->setMethodTitle($helper->__('To the door'))
                ->setPrice($price[1])
                ->setCost($price[1]);

            $result->append($method);

            if(!Mage::app()->getStore()->isAdmin()){
                $method = Mage::getModel('shipping/rate_result_method');
                $method->setCarrier($this->_code)
                    ->setCarrierTitle($this->getConfigData('name'))
                    ->setMethod('type_WarehouseWarehouse')
                    ->setMethodTitle($helper->__('To the warehouse'))
                    ->setPrice($price[0])
                    ->setCost($price[0]);
                $result->append($method);
            }

            return $result;
        }
    }
    public function getAllowedMethods(){
        return array($this->_code => $this->getConfigData('name'));
    }
    public function isTrackingAvailable(){
        return true;
    }
    protected function getCartWeight(){ 
        return SY_Novaposhta_Helper_Data::getCartWeight();
    }
}
