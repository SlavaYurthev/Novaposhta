<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Block_Options extends Mage_Core_Block_Template {
	public function getValue($option){
		$value = array();
		if($this->getValues() && count($this->getValues())>0){
			foreach ($this->getValues() as $key) {
				$value[] = $option[$key];
			}
		}
		$value = implode(" ", $value);
		return htmlentities($value);
	}
	public function getInner($option){
		$value = array();
		if($this->getInners() && count($this->getInners())>0){
			foreach ($this->getInners() as $key) {
				$value[] = $option[$key];
			}
		}
		$value = implode(" ", $value);
		return htmlentities($value);
	}
}