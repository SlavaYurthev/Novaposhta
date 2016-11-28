<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Model_Api_Client_Helper extends SY_Novaposhta_Model_Api_Client {
	public function getCityWarehouses()
    {
        $response = $this->getConnection()->getCities();
        $result = array();
        if (isset($response['data']) && is_array($response['data'])) {
            foreach ($response['data'] as $key => $city) {
                $this->_cityMarshaling($city, $key);
                $result[$city['ref']] = $city;
            }
        }
        return $result;
    }
    public function _cityMarshaling(array &$cityData, $key)
    {
        $data = array();
        $data['name_ru'] = $cityData['DescriptionRu'];
        $data['name_ua'] = $cityData['Description'];
        $data['ref']     = $cityData['Ref'];
        $data['id']      = $cityData['CityID'];

        $cityData = $data;
    }
    public function _warehouseMarshaling(array &$warehouseData, $key)
    {
        $data = array();
        $data['ref']      = $warehouseData['Ref'];
        $data['address_ru'] = $warehouseData['DescriptionRu'];
        $data['address_ua'] = $warehouseData['Description'];
        $data['phone']     = $warehouseData['Phone'];
        $data['longitude'] = $warehouseData['Longitude'];
        $data['latitude'] = $warehouseData['Latitude'];
        $data['max_weight_allowed'] = $warehouseData['TotalMaxWeightAllowed'];
        $data['number_in_city'] = $warehouseData['Number'];

        $warehouseData = $data;
    }
    public function getWarehouses(Varien_Object $cityInfo)
    {
        $warehouses = array();
        $response = $this->getConnection()->getWarehouses($cityInfo['ref']);
        if (isset($response['data']) && is_array($response['data'])) {
            foreach ($response['data'] as $key => $value) {
                $this->_warehouseMarshaling($value, $key);
                $value['city_id']          = $cityInfo['id'];
                $warehouses[$value['ref']] = $value;
            }
        }
        return $warehouses;
    }
}