<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Block_Adminhtml_Sales_Order_Grid_Register extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row){
		$value =  $row->getData($this->getColumn()->getIndex());
		$html = $value;
		if($value){
			$el_id = $this->getColumn()->getIndex().'_'.$row->getId();
			$html .= '<br>';
			$html .= '<select id="'.$el_id.'" style="text-align:center;">';
			$html .= '<option></option>';
			$html .= '<option value="https://my.novaposhta.ua/scanSheet/printScanSheet/refs[]/'.$value.'/type/pdf/apiKey/'.Mage::helper('sy_novaposhta')->getStoreConfig('api_key').'">'.Mage::helper('sy_novaposhta')->__('Pdf').'</option>';
			$html .= '<option value="https://my.novaposhta.ua/scanSheet/printScanSheet/refs[]/'.$value.'/type/html/apiKey/'.Mage::helper('sy_novaposhta')->getStoreConfig('api_key').'">'.Mage::helper('sy_novaposhta')->__('Html').'</option>';
			$html .= '</select><br>';
			$html .= '<a href="javascript:if(document.getElementById('."'".$el_id."'".').options[document.getElementById('."'".$el_id."'".').selectedIndex].value){window.open(document.getElementById('."'".$el_id."'".').options[document.getElementById('."'".$el_id."'".').selectedIndex].value)}">'.Mage::helper('sy_novaposhta')->__('Print').'</a>';
		}
		return $html;
	}
}