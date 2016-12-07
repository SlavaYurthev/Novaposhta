<?php
ini_set("memory_limit", "-1");
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Model_Observer extends Mage_Core_Model_Abstract {
	public function updateShippingInformation(Varien_Event_Observer $observer){
		$order = $observer->getEvent()->getOrder();
		$quote = $observer->getEvent()->getQuote();
		/*
		* Т.к. в админке мы используем список методов в его стандартном представлении
		* нам необходимо изменить код метода обрезав ссылку на склад из метода
		* сохранив её на место своего аттрибута "novaposhta_warehouse"
		* и наоборот т.к. на фронтальной части мы используем кастомный список
		* нам необходимо изменить описание доставки на "novaposhta_description"
		* в которое оно было сохранено перед тем как order будет сохранён
		* что бы в дальнейшем описание доставки включало в себя описание склада
		*/
		if(strpos($order->getData('shipping_method'), 'sy_novaposhta') !== false && 
			strpos($order->getData('shipping_method'), 'WarehouseWarehouse') !== false &&
			!$quote->getData('novaposhta_warehouse')){
			$order->setData('novaposhta_warehouse', str_replace('sy_novaposhta_type_WarehouseWarehouse_', '', $order->getData('shipping_method')));
			$order->setData('shipping_method', 'sy_novaposhta_type_WarehouseWarehouse');
		}
		elseif(strpos($order->getData('shipping_method'), 'sy_novaposhta') !== false && 
			strpos($order->getData('shipping_method'), 'WarehouseWarehouse') !== false &&
			$quote->getData('novaposhta_warehouse') && $quote->getData('novaposhta_description')){
			$order->setData('shipping_description', $quote->getData('novaposhta_description'));
		}
	}
	public function addColumnToSalesOrderGrid(Varien_Event_Observer $observer){
		if((bool)Mage::helper('sy_novaposhta')->getStoreConfig('api_key') !== false && 
			Mage::helper('sy_novaposhta')->getStoreConfig('active') == "1"){
			$block = $observer->getBlock();
		    if (!isset($block)) {
		        return $this;
		    }
		    if ($block->getType() == 'adminhtml/sales_order_grid') {
		    	$collection = Mage::getModel('sales/order')->getCollection()->addAttributeToSelect('novaposhta_status');
		    	$collection->addFieldToFilter('novaposhta_status', array('notnull'=>true));
		    	$collection->getSelect()->group('novaposhta_status');
		    	$values = $collection->getColumnValues('novaposhta_status');
				$block->addColumnAfter('novaposhta_status', array(
			        'header' => Mage::helper('sy_novaposhta')->__('Novaposhta: Status'),
			        'index' => 'novaposhta_status',
			        'filter_index' => 'novaposhta_status',
			        'type' => 'options',
			        'align' => 'center',
			        'options' => $values,
			    ), 'status');
				$block->addColumnAfter('novaposhta_barcode', array(
			        'header' => Mage::helper('sy_novaposhta')->__('Novaposhta: Barcode'),
			        'index' => 'novaposhta_barcode',
			        'filter_index' => 'novaposhta_barcode',
			        'align' => 'center',
			    ), 'novaposhta_status');
				$block->addColumnAfter('novaposhta_register', array(
			        'header' => Mage::helper('sy_novaposhta')->__('Novaposhta: Register'),
			        'index' => 'novaposhta_register',
			        'filter_index' => 'novaposhta_register',
			        'renderer'  => 'SY_Novaposhta_Block_Adminhtml_Sales_Order_Grid_Register',
			        'align' => 'center',
			        'filter' => false,
			        'sortable' => false,
			    ), 'novaposhta_barcode');
		    }
			if($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction && $block->getRequest()->getControllerName() == 'sales_order'){
				$block->addItem('novaposhta_register_add', array(
					'label' => Mage::helper('sy_novaposhta')->__('Novaposhta: Add to Register'),
					'url' => $block->getUrl('adminhtml/novaposhta_register/add')
				));
				// $block->addItem('novaposhta_register_delete', array(
				// 	'label' => Mage::helper('sy_novaposhta')->__('Novaposhta: Delete from Register'),
				// 	'url' => $block->getUrl('adminhtml/novaposhta_register/delete')
				// ));
				$block->addItem('novaposhta_print_marks', array(
					'label' => Mage::helper('sy_novaposhta')->__('Novaposhta: Print Marks').' (pdf)',
					'url' => $block->getUrl('adminhtml/novaposhta_print/marks')
				));
				$block->addItem('novaposhta_print_ttn', array(
					'label' => Mage::helper('sy_novaposhta')->__('Novaposhta: Print TTN').' (pdf)',
					'url' => $block->getUrl('adminhtml/novaposhta_print/ttn')
				));
				$block->addItem('novaposhta_print_marks_html', array(
					'label' => Mage::helper('sy_novaposhta')->__('Novaposhta: Print Marks').' (html)',
					'url' => $block->getUrl('adminhtml/novaposhta_print/markshtml')
				));
				$block->addItem('novaposhta_print_ttn_html', array(
					'label' => Mage::helper('sy_novaposhta')->__('Novaposhta: Print TTN').' (html)',
					'url' => $block->getUrl('adminhtml/novaposhta_print/ttnhtml')
				));
			}
		}
	}
	public function addAttributeToSalesOrderGrid(Varien_Event_Observer $observer){
		$collection = $observer->getOrderGridCollection();
		$collection->getSelect()->join(
		    array('s' => $collection->getTable('sales/order')),
		    'main_table.increment_id = s.increment_id',
		    array('novaposhta_status' => 's.novaposhta_status',
		    		'novaposhta_barcode' => 's.novaposhta_barcode',
		    		'novaposhta_register' => 's.novaposhta_register')
		);
	}
	public function updateStatuses(){
		if((bool)Mage::helper('sy_novaposhta')->getStoreConfig('api_key') !== false && 
			Mage::helper('sy_novaposhta')->getStoreConfig('active') == "1"){
			$collection = Mage::getModel('sales/order')->getCollection();
			$collection->addFieldToFilter('state',array('nin'=>array('closed','complete','canceled','holded')));
			$collection->addFieldToFilter('novaposhta_ref', array('notnull'=>true));
			if($collection->count()>0){
				$helper = Mage::helper('sy_novaposhta');
				foreach ($collection as $order) {
					$helper->updateStatus($order);
				}
			}
		}
	}
	public function updateData(){
		if((bool)Mage::helper('sy_novaposhta')->getStoreConfig('api_key') !== false && 
			Mage::helper('sy_novaposhta')->getStoreConfig('active') == "1" &&
			Mage::helper('sy_novaposhta')->getStoreConfig('on_air') == "0"){
			$varDir = Mage::getBaseDir('var').DS.'Novaposhta'.DS;
			foreach (scandir($varDir) as $file) {
				if($file != "." || $file != ".."){
					if(is_file($varDir.$file)){
						$filePath = $varDir.$file;
						$data = json_decode(file_get_contents($filePath), true);
						if($data && count($data)>0){
							$resource = Mage::getSingleton('core/resource');
							$write = $resource->getConnection('core_write');
							$table_cities = $resource->getTableName('sy_novaposhta_cities');
							$table_warehouses = $resource->getTableName('sy_novaposhta_warehouses');
							$table_streets = $resource->getTableName('sy_novaposhta_streets');
							$write->query("TRUNCATE TABLE `{$table_cities}`");
							$write->query("TRUNCATE TABLE `{$table_warehouses}`");
							$write->query("TRUNCATE TABLE `{$table_streets}`");
							foreach ($data as $item) {
								$model = Mage::getModel('sy_novaposhta/cities');
								$warehouses = $item['warehouses'];
								$streets = $item['streets'];
								$model->setData($item);
								$model->save();
								unset($model);
								if($warehouses && count($warehouses)>0){
									foreach ($warehouses as $warehouse) {
										$model = Mage::getModel('sy_novaposhta/warehouses');
										$model->setData($warehouse);
										$model->save();
										unset($model);
									}
								}
								if($streets && count($streets)>0){
									foreach ($streets as $street) {
										$model = Mage::getModel('sy_novaposhta/streets');
										$model->setData(array_merge($street, array('CityRef'=>$item['Ref'])));
										$model->save();
										unset($model);
									}
								}
							}
						}
						@unlink($filePath);
					}
				}
			}
		}
	}
	public function downloadData(){
		if((bool)Mage::helper('sy_novaposhta')->getStoreConfig('api_key') !== false && 
			Mage::helper('sy_novaposhta')->getStoreConfig('active') == "1" &&
			Mage::helper('sy_novaposhta')->getStoreConfig('on_air') == "0"){
			$helper = Mage::helper('sy_novaposhta');
			$cities = $helper->getCities(true);
			$varDir = Mage::getBaseDir('var').DS.'Novaposhta'.DS;
			if($cities && count($cities)>0){
				foreach ($cities as $key => $city) {
					try {
						$ref = $city['Ref'];
						$warehouses = $helper->findWarehouses($ref, true);
						$streets = $helper->findStreets($ref, true);
						if($warehouses && count($warehouses)>0){
							$city['warehouses'] = $warehouses;
						}
						if($streets && count($streets)>0){
							$city['streets'] = $streets;
						}
					} catch (Exception $e) {}
					$cities[$key] = $city;
				}
				file_put_contents($varDir.date("Y-m-d H:i:s").'.json', json_encode($cities));
				file_put_contents(Mage::getBaseDir('var').DS.date("Y-m-d H:i:s").'.json', json_encode($cities));
			}
		}
	}
}