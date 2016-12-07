<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
$installer = $this;

$installer->startSetup();

$sales_quote_address = $installer->getTable('sales/quote_address');
$installer->getConnection()
      ->addColumn($sales_quote_address, 'house', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'comment' => 'House'
      ));
$sales_order_address = $installer->getTable('sales/order_address');
$installer->getConnection()
      ->addColumn($sales_order_address, 'house', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'comment' => 'House'
      ));

$installer->endSetup();