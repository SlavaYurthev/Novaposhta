<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Adminhtml_Novaposhta_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function citiesAction(){
        $this->loadLayout();
        $this->_setActiveMenu('sy');
        $helper = Mage::helper('sy_novaposhta');
        $this->getLayout()->getBlock('head')->setTitle($helper->__('Cities Management'));
        $contentBlock = $this->getLayout()->createBlock('sy_novaposhta/adminhtml_cities');
        $this->_addContent($contentBlock);
        $this->renderLayout();
    }
    public function warehousesAction(){
        $this->loadLayout();
        $this->_setActiveMenu('sy');
        $helper = Mage::helper('sy_novaposhta');
        $this->getLayout()->getBlock('head')->setTitle($helper->__('Warehouses Management'));
        $contentBlock = $this->getLayout()->createBlock('sy_novaposhta/adminhtml_warehouses');
        $this->_addContent($contentBlock);
        $this->renderLayout();
    }
    public function streetsAction(){
        $this->loadLayout();
        $this->_setActiveMenu('sy');
        $helper = Mage::helper('sy_novaposhta');
        $this->getLayout()->getBlock('head')->setTitle($helper->__('Streets Management'));
        $contentBlock = $this->getLayout()->createBlock('sy_novaposhta/adminhtml_streets');
        $this->_addContent($contentBlock);
        $this->renderLayout();
    }
}