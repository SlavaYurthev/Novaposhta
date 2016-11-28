<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Block_Adminhtml_Cities extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		parent::__construct(); 
    	$this->_removeButton('add');
	}
    protected function _construct()
    {
        parent::_construct();

        $helper = Mage::helper('sy_novaposhta');
        $this->_blockGroup = 'sy_novaposhta';
        $this->_controller = 'adminhtml_cities';

        $this->_headerText = $helper->__('Cities Management');
    }
}