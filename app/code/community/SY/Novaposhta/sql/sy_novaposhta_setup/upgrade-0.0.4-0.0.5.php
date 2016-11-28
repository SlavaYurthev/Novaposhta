<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
$installer = $this;

$installer->startSetup();

$setup = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$setup->addAttribute('order', 'novaposhta_register', array(
            'group'             => 'General',
            'label'             => 'Novaposhta Register',
            'note'              => '',
            'type'              => 'varchar',   
            'input'             => 'text',
            'frontend_class'    => '',
            'source'            => '',
            'backend'           => '',
            'frontend'          => '',
            'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'required'          => false,
            'visible_on_front'  => false,
            'is_configurable'   => false,
            'used_in_product_listing'   => false,
        )
);
$installer->endSetup();