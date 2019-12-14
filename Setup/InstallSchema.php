<?php

namespace FME\Prodfaqs\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
        
        
        
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
                
                
        $installer = $setup;
        $installer->startSetup();
                
                
        /**
                * Create table 'fme_faq'
                */
        $table = $installer->getConnection()->newTable($installer->getTable('fme_faq'))->addColumn(
            'faq_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'unsigned' => true,'primary' => true],
            'Faq ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Title / Question'
        )->addColumn(
            'faq_answer',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true, 'default' => null],
            'Answer'
        )->addColumn(
            'show_on_main',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Show On Main Page'
        )->addColumn(
            'faq_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Sorting Order'
        )->addColumn(
            'rating_num',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Rating Number'
        )->addColumn(
            'rating_count',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Rating Count'
        )->addColumn(
            'rating_stars',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Rating Stars'
        )->addColumn(
            'accordion_opened',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Accordian Open'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Status'
        )->addColumn(
            'create_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Creation Date'
        )->addColumn(
            'update_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Update Date'
        );
        $installer->getConnection()->createTable($table);
                
        $installer->endSetup();
    }
}
