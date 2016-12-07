<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
$installer = $this;
$installer->startSetup();
$table_streets = $installer->getTable('sy_novaposhta/sy_novaposhta_streets');

// $installer->getConnection()->dropTable($table_streets);

$table = $installer->getConnection()
    ->newTable($table_streets)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ))
    ->addColumn('Description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        ))
    ->addColumn('Ref', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
        'nullable'  => false,
        ))
    ->addColumn('StreetsTypeRef', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
        'nullable'  => false,
        ))
    ->addColumn('StreetsType', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
        'nullable'  => false,
        ))
    ->addColumn('CityRef', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
        'nullable'  => false,
        ));
$installer->getConnection()->createTable($table);

$installer->endSetup();