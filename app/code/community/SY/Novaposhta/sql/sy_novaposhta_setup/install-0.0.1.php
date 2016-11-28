<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
$installer = $this;

$installer->startSetup();

$setup = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$setup->addAttribute('order', 'novaposhta_barcode', array(
            'group'             => 'General',
            'label'             => 'Novaposhta Barcode',
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
$setup->addAttribute('order', 'novaposhta_status', array(
            'group'             => 'General',
            'label'             => 'Novaposhta Status',
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
$setup->addAttribute('order', 'novaposhta_ref', array(
            'group'             => 'General',
            'label'             => 'Novaposhta Ref',
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